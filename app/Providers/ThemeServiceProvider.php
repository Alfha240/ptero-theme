<?php

namespace Pterodactyl\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Pterodactyl\Models\ThemeSetting;
use Illuminate\Support\Facades\Schema;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent issues during migration if table doesn't exist
        if (!Schema::hasTable('theme_settings')) {
            return;
        }

        View::composer(['layouts.admin', 'templates.wrapper', 'layouts.auth'], function (\Illuminate\View\View $view) {
            try {
                $css = ThemeSetting::getCss();
                $customThemeCss = $css ? "<style>{$css}</style>" : '';
                $view->with('customThemeCss', $customThemeCss);
            } catch (\Exception $e) {
                // Fail silently to avoid breaking the panel if DB issue
                $view->with('customThemeCss', '');
            }
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
