<?php

namespace Pterodactyl\Http\Controllers\Admin;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Models\ThemeSetting;
use Illuminate\Http\Request;
use Prologue\Alerts\AlertsMessageBag;

class ThemeController extends Controller
{
    /**
     * ThemeController constructor.
     */
    public function __construct(private AlertsMessageBag $alert)
    {
    }

    /**
     * Render the theme customizer page.
     */
    public function index(): View
    {
        $setting = ThemeSetting::firstOrNew();

        return view('admin.theme.index', [
            'setting' => $setting,
        ]);
    }

    /**
     * Handle saving theme settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'theme_enabled' => 'nullable|in:1,on,true',
            'custom_css' => 'nullable|string',
            // Colors
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'background_color' => 'nullable|string',
            'text_color' => 'nullable|string',
            'gradient_start' => 'nullable|string',
            'gradient_end' => 'nullable|string',
            // Design
            'card_style' => 'nullable|string|in:solid,glass,outline',
            'sidebar_style' => 'nullable|string|in:full,compact,minimized',
            'border_radius' => 'nullable|string',
            'blur_intensity' => 'nullable|string',
            // Branding
            'app_name' => 'nullable|string|max:255',
            'logo_url' => 'nullable|string|url',
            'favicon_url' => 'nullable|string|url',
            'login_background_url' => 'nullable|string|url',
            // Layout (Toggles)
            'dashboard_layout' => 'nullable|array',
        ]);

        $setting = ThemeSetting::firstOrNew();
        
        // Manual boolean handling
        $setting->theme_enabled = $request->has('theme_enabled');

        // Allow mass assignment for the rest (safer since we validated)
        $setting->fill($data);
        
        // Ensure dashboard_layout is saved as array (Laravel casts handles JSON encoding)
        if (isset($data['dashboard_layout'])) {
            $setting->dashboard_layout = $data['dashboard_layout'];
        }

        $setting->save();

        $this->alert->success('Theme settings have been updated successfully.')->flash();

        return redirect()->route('admin.theme');
    }
}
