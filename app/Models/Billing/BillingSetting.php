<?php

namespace Pterodactyl\Models\Billing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BillingSetting extends Model
{
    use HasFactory;

    protected $table = 'billing_settings';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("billing_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("billing_setting_{$key}");
    }

    /**
     * Get coins per ad view.
     */
    public static function coinsPerAd(): int
    {
        return (int) static::get('coins_per_ad', 10);
    }

    /**
     * Get ad cooldown in seconds.
     */
    public static function adCooldown(): int
    {
        return (int) static::get('ad_cooldown_seconds', 60);
    }

    /**
     * Get daily reward coins.
     */
    public static function dailyRewardCoins(): int
    {
        return (int) static::get('daily_reward_coins', 50);
    }

    /**
     * Get max servers per user.
     */
    public static function maxServersPerUser(): int
    {
        return (int) static::get('max_servers_per_user', 2);
    }

    /**
     * Get new user bonus coins.
     */
    public static function newUserBonus(): int
    {
        return (int) static::get('new_user_bonus', 100);
    }
}
