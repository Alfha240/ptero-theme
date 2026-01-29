<?php

namespace Pterodactyl\Models\Billing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pterodactyl\Models\User;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'billing_wallets';

    protected $fillable = [
        'user_id',
        'coins',
        'total_earned',
        'total_spent',
        'last_daily_claim',
    ];

    protected $casts = [
        'coins' => 'integer',
        'total_earned' => 'integer',
        'total_spent' => 'integer',
        'last_daily_claim' => 'datetime',
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get wallet transactions.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id');
    }

    /**
     * Add coins to wallet.
     */
    public function addCoins(int $amount, string $source, ?string $referenceId = null, ?string $description = null): Transaction
    {
        $this->increment('coins', $amount);
        $this->increment('total_earned', $amount);

        return Transaction::create([
            'user_id' => $this->user_id,
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $this->coins,
            'source' => $source,
            'reference_id' => $referenceId,
            'description' => $description,
        ]);
    }

    /**
     * Deduct coins from wallet.
     */
    public function deductCoins(int $amount, string $source, ?string $referenceId = null, ?string $description = null): ?Transaction
    {
        if ($this->coins < $amount) {
            return null; // Insufficient balance
        }

        $this->decrement('coins', $amount);
        $this->increment('total_spent', $amount);

        return Transaction::create([
            'user_id' => $this->user_id,
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $this->coins,
            'source' => $source,
            'reference_id' => $referenceId,
            'description' => $description,
        ]);
    }

    /**
     * Check if user has enough coins.
     */
    public function hasEnoughCoins(int $amount): bool
    {
        return $this->coins >= $amount;
    }

    /**
     * Check if daily reward is available.
     */
    public function canClaimDaily(): bool
    {
        if (!$this->last_daily_claim) {
            return true;
        }

        return $this->last_daily_claim->addHours(24)->isPast();
    }

    /**
     * Get time until next daily reward.
     */
    public function timeUntilDailyReward(): ?int
    {
        if ($this->canClaimDaily()) {
            return null;
        }

        return $this->last_daily_claim->addHours(24)->diffInSeconds(now());
    }
}
