<?php

namespace Pterodactyl\Models\Billing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pterodactyl\Models\User;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'billing_transactions';

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_after',
        'source',
        'reference_id',
        'description',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
    ];

    /**
     * Get the user for this transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to only credits.
     */
    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    /**
     * Scope to only debits.
     */
    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }

    /**
     * Get icon based on source.
     */
    public function getIconAttribute(): string
    {
        return match ($this->source) {
            'ad' => '📺',
            'daily' => '🎁',
            'code' => '🎟️',
            'server' => '🖥️',
            'admin' => '👑',
            'referral' => '👥',
            'bonus' => '🎉',
            default => '💰',
        };
    }

    /**
     * Get formatted amount with sign.
     */
    public function getFormattedAmountAttribute(): string
    {
        $sign = $this->type === 'credit' ? '+' : '-';
        return $sign . number_format($this->amount);
    }
}
