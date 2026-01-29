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
            'theme_enabled' => 'nullable|in:1,on,true', // Checkbox handling
            'custom_css' => 'nullable|string',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'background_color' => 'nullable|string',
            'text_color' => 'nullable|string',
            'gradient_start' => 'nullable|string',
            'gradient_end' => 'nullable|string',
        ]);

        // Handle checkbox boolean conversion
        $enabled = $request->has('theme_enabled');

        $setting = ThemeSetting::firstOrNew();
        $setting->theme_enabled = $enabled;
        $setting->custom_css = $data['custom_css'] ?? '';
        $setting->primary_color = $data['primary_color'] ?? null;
        $setting->secondary_color = $data['secondary_color'] ?? null;
        $setting->background_color = $data['background_color'] ?? null;
        $setting->text_color = $data['text_color'] ?? null;
        $setting->gradient_start = $data['gradient_start'] ?? null;
        $setting->gradient_end = $data['gradient_end'] ?? null;
        $setting->save();

        $this->alert->success('Theme settings have been updated successfully.')->flash();

        return redirect()->route('admin.theme');
    }
}
