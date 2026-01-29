<?php

namespace Pterodactyl\Models\Billing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingPlan extends Model
{
    use HasFactory;

    protected $table = 'billing_plans';

    protected $fillable = [
        'name',
        'description',
        'memory',
        'disk',
        'cpu',
        'databases',
        'backups',
        'allocations',
        'coins_per_minute',
        'creation_cost',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'memory' => 'integer',
        'disk' => 'integer',
        'cpu' => 'integer',
        'databases' => 'integer',
        'backups' => 'integer',
        'allocations' => 'integer',
        'coins_per_minute' => 'integer',
        'creation_cost' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to only active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get formatted memory.
     */
    public function getFormattedMemoryAttribute(): string
    {
        if ($this->memory >= 1024) {
            return round($this->memory / 1024, 1) . ' GB';
        }
        return $this->memory . ' MB';
    }

    /**
     * Get formatted disk.
     */
    public function getFormattedDiskAttribute(): string
    {
        if ($this->disk >= 1024) {
            return round($this->disk / 1024, 1) . ' GB';
        }
        return $this->disk . ' MB';
    }

    /**
     * Get formatted CPU.
     */
    public function getFormattedCpuAttribute(): string
    {
        return $this->cpu . '%';
    }

    /**
     * Calculate hourly cost.
     */
    public function getHourlyCostAttribute(): int
    {
        return $this->coins_per_minute * 60;
    }

    /**
     * Calculate daily cost.
     */
    public function getDailyCostAttribute(): int
    {
        return $this->coins_per_minute * 60 * 24;
    }
}
