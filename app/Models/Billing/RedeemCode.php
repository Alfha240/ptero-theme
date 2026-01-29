<?php

namespace Pterodactyl\Models\Billing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedeemCode extends Model
{
    use HasFactory;

    protected $table = 'billing_codes';

    protected $fillable = [
        'code',
        'coins',
        'max_uses',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'coins' => 'integer',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Check if code is valid and can be used.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Check if user has already used this code.
     */
    public function hasBeenUsedBy(int $userId): bool
    {
        return CodeUse::where('code_id', $this->id)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Record code use.
     */
    public function recordUse(int $userId): void
    {
        CodeUse::create([
            'code_id' => $this->id,
            'user_id' => $userId,
        ]);

        $this->increment('used_count');
    }

    /**
     * Uses relationship.
     */
    public function uses()
    {
        return $this->hasMany(CodeUse::class, 'code_id');
    }
}
