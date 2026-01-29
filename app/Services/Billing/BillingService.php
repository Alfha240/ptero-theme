<?php

namespace Pterodactyl\Services\Billing;

use Pterodactyl\Models\User;
use Pterodactyl\Models\Billing\Wallet;
use Pterodactyl\Models\Billing\BillingPlan;
use Pterodactyl\Models\Billing\BillingSetting;
use Pterodactyl\Models\Billing\RedeemCode;
use Pterodactyl\Models\Billing\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillingService
{
    /**
     * Get or create wallet for user.
     */
    public function getOrCreateWallet(User $user): Wallet
    {
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'coins' => BillingSetting::newUserBonus(),
            ]);

            // Record signup bonus transaction
            if (BillingSetting::newUserBonus() > 0) {
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'amount' => BillingSetting::newUserBonus(),
                    'balance_after' => $wallet->coins,
                    'source' => 'bonus',
                    'description' => 'Welcome bonus for new users',
                ]);
            }
        }

        return $wallet;
    }

    /**
     * Claim daily reward.
     */
    public function claimDailyReward(User $user): array
    {
        $wallet = $this->getOrCreateWallet($user);

        if (!$wallet->canClaimDaily()) {
            return [
                'success' => false,
                'message' => 'Daily reward not available yet',
                'next_claim_in' => $wallet->timeUntilDailyReward(),
            ];
        }

        $coins = BillingSetting::dailyRewardCoins();
        $wallet->addCoins($coins, 'daily', null, 'Daily reward claim');
        $wallet->update(['last_daily_claim' => now()]);

        return [
            'success' => true,
            'coins_earned' => $coins,
            'new_balance' => $wallet->fresh()->coins,
        ];
    }

    /**
     * Process ad view and earn coins.
     */
    public function processAdView(User $user): array
    {
        $wallet = $this->getOrCreateWallet($user);
        $cooldown = BillingSetting::adCooldown();

        // Check cooldown
        $lastAdView = DB::table('billing_ad_views')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastAdView) {
            $lastViewTime = Carbon::parse($lastAdView->created_at);
            $secondsSinceLastView = now()->diffInSeconds($lastViewTime);

            if ($secondsSinceLastView < $cooldown) {
                return [
                    'success' => false,
                    'message' => 'Please wait before watching another ad',
                    'cooldown_remaining' => $cooldown - $secondsSinceLastView,
                ];
            }
        }

        $coins = BillingSetting::coinsPerAd();

        // Record ad view
        DB::table('billing_ad_views')->insert([
            'user_id' => $user->id,
            'ad_type' => 'video',
            'coins_earned' => $coins,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add coins
        $wallet->addCoins($coins, 'ad', null, 'Watched video ad');

        return [
            'success' => true,
            'coins_earned' => $coins,
            'new_balance' => $wallet->fresh()->coins,
            'next_ad_in' => $cooldown,
        ];
    }

    /**
     * Redeem a code.
     */
    public function redeemCode(User $user, string $code): array
    {
        $wallet = $this->getOrCreateWallet($user);

        $redeemCode = RedeemCode::where('code', strtoupper(trim($code)))->first();

        if (!$redeemCode) {
            return [
                'success' => false,
                'message' => 'Invalid code',
            ];
        }

        if (!$redeemCode->isValid()) {
            return [
                'success' => false,
                'message' => 'This code has expired or reached its usage limit',
            ];
        }

        if ($redeemCode->hasBeenUsedBy($user->id)) {
            return [
                'success' => false,
                'message' => 'You have already used this code',
            ];
        }

        // Redeem the code
        $redeemCode->recordUse($user->id);
        $wallet->addCoins($redeemCode->coins, 'code', $redeemCode->id, "Redeemed code: {$code}");

        return [
            'success' => true,
            'coins_earned' => $redeemCode->coins,
            'new_balance' => $wallet->fresh()->coins,
        ];
    }

    /**
     * Get available plans.
     */
    public function getActivePlans()
    {
        return BillingPlan::active()->ordered()->get();
    }

    /**
     * Check if user can create a server.
     */
    public function canCreateServer(User $user, BillingPlan $plan): array
    {
        $wallet = $this->getOrCreateWallet($user);

        // Check server limit
        $currentServers = $user->servers()->count();
        $maxServers = BillingSetting::maxServersPerUser();

        if ($currentServers >= $maxServers) {
            return [
                'can_create' => false,
                'reason' => "You have reached the maximum of {$maxServers} servers",
            ];
        }

        // Check coins for creation cost
        if ($plan->creation_cost > 0 && !$wallet->hasEnoughCoins($plan->creation_cost)) {
            return [
                'can_create' => false,
                'reason' => "Not enough coins. You need {$plan->creation_cost} coins to create this server",
            ];
        }

        // Check coins for at least 1 hour of runtime
        $hourCost = $plan->coins_per_minute * 60;
        $requiredCoins = $plan->creation_cost + $hourCost;

        if (!$wallet->hasEnoughCoins($requiredCoins)) {
            return [
                'can_create' => false,
                'reason' => "You need at least {$requiredCoins} coins (creation + 1 hour runtime)",
            ];
        }

        return [
            'can_create' => true,
            'creation_cost' => $plan->creation_cost,
            'hourly_cost' => $hourCost,
        ];
    }

    /**
     * Charge user for server creation.
     */
    public function chargeServerCreation(User $user, BillingPlan $plan, int $serverId): bool
    {
        if ($plan->creation_cost <= 0) {
            return true;
        }

        $wallet = $this->getOrCreateWallet($user);
        $transaction = $wallet->deductCoins(
            $plan->creation_cost,
            'server',
            $serverId,
            "Server creation fee for plan: {$plan->name}"
        );

        return $transaction !== null;
    }

    /**
     * Get user's transaction history.
     */
    public function getTransactionHistory(User $user, int $limit = 20)
    {
        return Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user stats.
     */
    public function getUserStats(User $user): array
    {
        $wallet = $this->getOrCreateWallet($user);

        return [
            'coins' => $wallet->coins,
            'total_earned' => $wallet->total_earned,
            'total_spent' => $wallet->total_spent,
            'servers_count' => $user->servers()->count(),
            'max_servers' => BillingSetting::maxServersPerUser(),
            'can_claim_daily' => $wallet->canClaimDaily(),
            'daily_cooldown' => $wallet->timeUntilDailyReward(),
        ];
    }
}
