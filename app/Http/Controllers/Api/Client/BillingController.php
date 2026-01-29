<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use Pterodactyl\Services\Billing\BillingService;
use Pterodactyl\Models\Billing\BillingPlan;
use Pterodactyl\Transformers\Api\Client\BaseClientTransformer;
use Pterodactyl\Http\Controllers\Api\Client\ClientApiController;

class BillingController extends ClientApiController
{
    private BillingService $billingService;

    public function __construct(BillingService $billingService)
    {
        parent::__construct();
        $this->billingService = $billingService;
    }

    /**
     * Get user's wallet info and stats.
     */
    public function wallet(Request $request)
    {
        $user = $request->user();
        $stats = $this->billingService->getUserStats($user);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get transaction history.
     */
    public function transactions(Request $request)
    {
        $user = $request->user();
        $limit = $request->input('limit', 20);
        
        $transactions = $this->billingService->getTransactionHistory($user, min($limit, 100));

        return response()->json([
            'success' => true,
            'data' => $transactions->map(function ($t) {
                return [
                    'id' => $t->id,
                    'type' => $t->type,
                    'amount' => $t->amount,
                    'formatted_amount' => $t->formatted_amount,
                    'balance_after' => $t->balance_after,
                    'source' => $t->source,
                    'icon' => $t->icon,
                    'description' => $t->description,
                    'created_at' => $t->created_at->toIso8601String(),
                ];
            }),
        ]);
    }

    /**
     * Claim daily reward.
     */
    public function claimDaily(Request $request)
    {
        $user = $request->user();
        $result = $this->billingService->claimDailyReward($user);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Process ad view.
     */
    public function earnFromAd(Request $request)
    {
        $user = $request->user();
        $result = $this->billingService->processAdView($user);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Redeem a code.
     */
    public function redeemCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $user = $request->user();
        $result = $this->billingService->redeemCode($user, $request->input('code'));

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get available plans.
     */
    public function plans(Request $request)
    {
        $plans = $this->billingService->getActivePlans();

        return response()->json([
            'success' => true,
            'data' => $plans->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'memory' => $plan->memory,
                    'memory_formatted' => $plan->formatted_memory,
                    'disk' => $plan->disk,
                    'disk_formatted' => $plan->formatted_disk,
                    'cpu' => $plan->cpu,
                    'cpu_formatted' => $plan->formatted_cpu,
                    'databases' => $plan->databases,
                    'backups' => $plan->backups,
                    'allocations' => $plan->allocations,
                    'coins_per_minute' => $plan->coins_per_minute,
                    'hourly_cost' => $plan->hourly_cost,
                    'daily_cost' => $plan->daily_cost,
                    'creation_cost' => $plan->creation_cost,
                ];
            }),
        ]);
    }

    /**
     * Check if user can create server with plan.
     */
    public function checkServerCreation(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer|exists:billing_plans,id',
        ]);

        $user = $request->user();
        $plan = BillingPlan::findOrFail($request->input('plan_id'));

        $result = $this->billingService->canCreateServer($user, $plan);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Get billing settings (public).
     */
    public function settings(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'coins_per_ad' => \Pterodactyl\Models\Billing\BillingSetting::coinsPerAd(),
                'ad_cooldown' => \Pterodactyl\Models\Billing\BillingSetting::adCooldown(),
                'daily_reward' => \Pterodactyl\Models\Billing\BillingSetting::dailyRewardCoins(),
                'max_servers' => \Pterodactyl\Models\Billing\BillingSetting::maxServersPerUser(),
                'new_user_bonus' => \Pterodactyl\Models\Billing\BillingSetting::newUserBonus(),
            ],
        ]);
    }
}
