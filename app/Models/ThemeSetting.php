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
        'card_style',
        'sidebar_style',
        'border_radius',
        'blur_intensity',
        'app_name',
        'logo_url',
        'favicon_url',
        'login_background_url',
        'dashboard_layout',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'theme_enabled' => 'boolean',
        'dashboard_layout' => 'array',
    ];

    /**
     * Generate the CSS block for the theme.
     */
    public static function getCss(): string
    {
        $setting = self::first();
        
        if (!$setting || !$setting->theme_enabled) {
            return '';
        }

        $css = [];

        // 1. Colors
        if ($setting->primary_color) $css[] = "--primary-color: {$setting->primary_color};";
        if ($setting->secondary_color) $css[] = "--secondary-color: {$setting->secondary_color};";
        if ($setting->background_color) $css[] = "--background-color: {$setting->background_color};";
        if ($setting->text_color) $css[] = "--text-color: {$setting->text_color};";
        if ($setting->gradient_start) $css[] = "--gradient-start: {$setting->gradient_start};";
        if ($setting->gradient_end) $css[] = "--gradient-end: {$setting->gradient_end};";

        // 2. Design Variables
        $radius = $setting->border_radius ?? '8px';
        $css[] = "--border-radius: {$radius};";
        
        // Glassmorphism Logic
        if (($setting->card_style ?? 'solid') === 'glass') {
            $blur = $setting->blur_intensity ?? '10px';
            $css[] = "--card-bg: rgba(30, 30, 40, 0.6);";
            $css[] = "--card-blur: blur({$blur});";
            $css[] = "--card-border: 1px solid rgba(255, 255, 255, 0.1);";
        } else {
             $css[] = "--card-bg: var(--secondary-color);";
             $css[] = "--card-blur: none;";
             $css[] = "--card-border: none;";
        }
        
        // Sidebar Width
        if (($setting->sidebar_style ?? 'full') === 'compact') {
             $css[] = "--sidebar-width: 80px;";
        } else {
             $css[] = "--sidebar-width: 250px;";
        }

        // 3. Layout Toggles (CSS-based Hiding)
        $layout = is_array($setting->dashboard_layout) ? $setting->dashboard_layout : json_decode($setting->dashboard_layout ?? '[]', true);
        
        $hideCss = "";
        // Note: These class names must match what we use in Pterodactyl React Components
        if (isset($layout['show_hero']) && !$layout['show_hero']) {
            $hideCss .= ".custom-dashboard-hero { display: none !important; } ";
        }
        if (isset($layout['show_graphs']) && !$layout['show_graphs']) {
             $hideCss .= ".custom-stat-graphs { display: none !important; } ";
        }
        if (isset($layout['show_cpu']) && !$layout['show_cpu']) {
             $hideCss .= ".custom-stat-cpu { display: none !important; } ";
        }
        if (isset($layout['show_memory']) && !$layout['show_memory']) {
             $hideCss .= ".custom-stat-memory { display: none !important; } ";
        }
        if (isset($layout['show_disk']) && !$layout['show_disk']) {
             $hideCss .= ".custom-stat-disk { display: none !important; } ";
        }

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
                width: var(--sidebar-width) !important;
                transition: width 0.3s ease;
            }

            /* Navigation Bar / Header */
            div[class*=\"NavigationBar__Container\"] {
                background-color: var(--secondary-color) !important;
            }

            /* Content Containers (Cards) */
            div[class*=\"ContentBox__Container\"], .custom-theme-stat-block, .custom-theme-chart-block {
                background-color: var(--card-bg) !important;
                backdrop-filter: var(--card-blur) !important;
                -webkit-backdrop-filter: var(--card-blur) !important;
                border: var(--card-border) !important;
                border-radius: var(--border-radius) !important;
            }

            /* Buttons */
            button[class*=\"Button__Container\"] {
                background: linear-gradient(to right, var(--gradient-start), var(--gradient-end)) !important;
                border: none !important;
                border-radius: var(--border-radius) !important;
                color: #fff !important;
            }

            /* Inputs */
            input, select, textarea, .xterm-viewport {
                background-color: var(--background-color) !important;
                color: var(--text-color) !important;
                border: 1px solid var(--secondary-color) !important;
                border-radius: var(--border-radius) !important;
            }
        ";

        // Append custom CSS
        return $rootBlock . "\n" . $overrides . "\n" . $hideCss . "\n" . ($setting->custom_css ?? '');
    }
}
