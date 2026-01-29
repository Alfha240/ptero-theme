<?php

namespace Pterodactyl\Models;

/**
 * Pterodactyl\Models\ThemeSetting.
 *
 * @property int $id
 * @property bool $theme_enabled
 * @property string|null $custom_css
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ThemeSetting extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'theme_settings';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'theme_enabled',
        'custom_css',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'theme_enabled' => 'boolean',
    ];

    /**
     * Get the current CSS if enabled, or empty string.
     */
    public static function getCss(): string
    {
        $setting = self::first();
        
        if (!$setting || !$setting->theme_enabled) {
            return '';
        }

        return $setting->custom_css ?? '';
    }
}
