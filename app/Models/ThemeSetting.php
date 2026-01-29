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
        'primary_color',
        'secondary_color',
        'background_color',
        'text_color',
        'gradient_start',
        'gradient_end',
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

        $css = [];

        // Generate CSS variables for colors
        if ($setting->primary_color) $css[] = "--primary-color: {$setting->primary_color};";
        if ($setting->secondary_color) $css[] = "--secondary-color: {$setting->secondary_color};";
        if ($setting->background_color) $css[] = "--background-color: {$setting->background_color};";
        if ($setting->text_color) $css[] = "--text-color: {$setting->text_color};";
        if ($setting->gradient_start) $css[] = "--gradient-start: {$setting->gradient_start};";
        if ($setting->gradient_end) $css[] = "--gradient-end: {$setting->gradient_end};";

        if ($setting->gradient_end) $css[] = "--gradient-end: {$setting->gradient_end};";

        // Create the root block
        $rootBlock = !empty($css) ? ":root { " . implode(' ', $css) . " }" : "";

        // Pterodactyl Standard overrides
        // We use [class*="..."] selectors to target styled-components with hashed classes
        $overrides = "
            /* Backgrounds */
            body, html, #app {
                background-color: var(--background-color) !important;
                color: var(--text-color) !important;
            }
            
            /* Sidebar */
            div[class*=\"Sidebar__Container\"] {
                background-color: var(--secondary-color) !important;
            }

            /* Navigation Bar / Header */
            div[class*=\"NavigationBar__Container\"] {
                background-color: var(--secondary-color) !important;
            }

            /* Content Containers */
            div[class*=\"ContentBox__Container\"] {
                background-color: var(--secondary-color) !important;
                border: 1px solid var(--primary-color) !important;
            }

            /* Buttons */
            button[class*=\"Button__Container\"] {
                background-color: var(--primary-color) !important;
            }

            /* Inputs */
            input, select, textarea {
                color: var(--text-color) !important;
            }

            /* Modals */
            div[class*=\"Modal__ModalContainer\"] {
                background-color: var(--background-color) !important;
            }
        ";

        // Append custom CSS
        return $rootBlock . "\n" . $overrides . "\n" . ($setting->custom_css ?? '');
    }
}
