@extends('layouts.admin')

@section('title')
    Theme Customizer
@endsection

@section('content-header')
    <h1>Theme Customizer<small>Customize the look and feel of your panel.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Theme Customizer</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <form action="{{ route('admin.theme') }}" method="POST">
            @csrf
            
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Theme Colors</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Design & Layout</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Branding</a></li>
                    <li><a href="#tab_4" data-toggle="tab">Dashboard Elements</a></li>
                </ul>
                <div class="tab-content">
                    
                    {{-- TAB 1: COLORS --}}
                    <div class="tab-pane active" id="tab_1">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="theme_enabled" class="control-label">Enable Custom Theme</label>
                                    <div>
                                        <input type="checkbox" name="theme_enabled" id="theme_enabled" {{ $setting->theme_enabled ? 'checked' : '' }}>
                                        <span class="help-block">Toggle this to enable or disable the custom CSS globally.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <label class="control-label">Quick Presets</label>
                                <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
                                    <div class="preset-card" onclick="applyPreset('purple')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center; width: 100px;">
                                        <div style="width: 20px; height: 20px; background: #6366f1; border-radius: 50%; margin: 0 auto 5px;"></div>
                                        <small>Purple</small>
                                    </div>
                                    <div class="preset-card" onclick="applyPreset('blue')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center; width: 100px;">
                                        <div style="width: 20px; height: 20px; background: #3b82f6; border-radius: 50%; margin: 0 auto 5px;"></div>
                                        <small>Blue</small>
                                    </div>
                                    <div class="preset-card" onclick="applyPreset('green')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center; width: 100px;">
                                        <div style="width: 20px; height: 20px; background: #10b981; border-radius: 50%; margin: 0 auto 5px;"></div>
                                        <small>Green</small>
                                    </div>
                                    <div class="preset-card" onclick="applyPreset('orange')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center; width: 100px;">
                                        <div style="width: 20px; height: 20px; background: #f59e0b; border-radius: 50%; margin: 0 auto 5px;"></div>
                                        <small>Orange</small>
                                    </div>
                                    <div class="preset-card" onclick="applyPreset('red')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center; width: 100px;">
                                        <div style="width: 20px; height: 20px; background: #ef4444; border-radius: 50%; margin: 0 auto 5px;"></div>
                                        <small>Red</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="primary_color" class="control-label">Primary Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control" name="primary_color" id="primary_color" value="{{ $setting->primary_color ?? '#6366f1' }}" style="height: 40px;">
                                        <span class="input-group-addon code-preview" id="preview_primary_color">{{ $setting->primary_color ?? '#6366f1' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="secondary_color" class="control-label">Secondary Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control" name="secondary_color" id="secondary_color" value="{{ $setting->secondary_color ?? '#3b82f6' }}" style="height: 40px;">
                                        <span class="input-group-addon code-preview" id="preview_secondary_color">{{ $setting->secondary_color ?? '#3b82f6' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="background_color" class="control-label">Background Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control" name="background_color" id="background_color" value="{{ $setting->background_color ?? '#0f0f18' }}" style="height: 40px;">
                                        <span class="input-group-addon code-preview" id="preview_background_color">{{ $setting->background_color ?? '#0f0f18' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="text_color" class="control-label">Text Color</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control" name="text_color" id="text_color" value="{{ $setting->text_color ?? '#ffffff' }}" style="height: 40px;">
                                        <span class="input-group-addon code-preview" id="preview_text_color">{{ $setting->text_color ?? '#ffffff' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gradient_start" class="control-label">Gradient Start</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control" name="gradient_start" id="gradient_start" value="{{ $setting->gradient_start ?? '#6366f1' }}" style="height: 40px;">
                                        <span class="input-group-addon code-preview" id="preview_gradient_start">{{ $setting->gradient_start ?? '#6366f1' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gradient_end" class="control-label">Gradient End</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control" name="gradient_end" id="gradient_end" value="{{ $setting->gradient_end ?? '#3b82f6' }}" style="height: 40px;">
                                        <span class="input-group-addon code-preview" id="preview_gradient_end">{{ $setting->gradient_end ?? '#3b82f6' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: DESIGN --}}
                    <div class="tab-pane" id="tab_2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="card_style" class="control-label">Card Style</label>
                                    <select name="card_style" id="card_style" class="form-control">
                                        <option value="solid" {{ ($setting->card_style ?? 'solid') == 'solid' ? 'selected' : '' }}>Solid (Fastest)</option>
                                        <option value="glass" {{ ($setting->card_style ?? 'solid') == 'glass' ? 'selected' : '' }}>Glassmorphism (Premium)</option>
                                        <option value="outline" {{ ($setting->card_style ?? 'solid') == 'outline' ? 'selected' : '' }}>Outline</option>
                                    </select>
                                    <p class="text-muted small">Glassmorphism adds a blur effect to cards. May affect performance on low-end devices.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sidebar_style" class="control-label">Sidebar Style</label>
                                    <select name="sidebar_style" id="sidebar_style" class="form-control">
                                        <option value="full" {{ ($setting->sidebar_style ?? 'full') == 'full' ? 'selected' : '' }}>Full Width</option>
                                        <option value="compact" {{ ($setting->sidebar_style ?? 'full') == 'compact' ? 'selected' : '' }}>Compact</option>
                                        <option value="minimized" {{ ($setting->sidebar_style ?? 'full') == 'minimized' ? 'selected' : '' }}>Minimized</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="border_radius" class="control-label">Border Radius</label>
                                    <input type="text" class="form-control" name="border_radius" value="{{ $setting->border_radius ?? '8px' }}" placeholder="e.g., 8px, 1rem">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="blur_intensity" class="control-label">Blur Intensity (for Glass)</label>
                                    <input type="text" class="form-control" name="blur_intensity" value="{{ $setting->blur_intensity ?? '10px' }}" placeholder="e.g., 10px">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 3: BRANDING --}}
                    <div class="tab-pane" id="tab_3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="app_name" class="control-label">Application Name</label>
                                    <input type="text" class="form-control" name="app_name" value="{{ $setting->app_name }}" placeholder="Pterodactyl">
                                    <p class="text-muted small">This replaces the "Pterodactyl" branding in the title bar.</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="logo_url" class="control-label">Logo URL</label>
                                    <input type="url" class="form-control" name="logo_url" value="{{ $setting->logo_url }}" placeholder="https://example.com/logo.png">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="favicon_url" class="control-label">Favicon URL</label>
                                    <input type="url" class="form-control" name="favicon_url" value="{{ $setting->favicon_url }}" placeholder="https://example.com/favicon.ico">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="login_background_url" class="control-label">Login Background URL</label>
                                    <input type="url" class="form-control" name="login_background_url" value="{{ $setting->login_background_url }}" placeholder="https://example.com/bg.jpg">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 4: DASHBOARD ELEMENTS --}}
                    <div class="tab-pane" id="tab_4">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-muted">Uncheck items to hide them from the server dashboard.</p>
                                @php
                                    $layout = is_array($setting->dashboard_layout) ? $setting->dashboard_layout : json_decode($setting->dashboard_layout, true) ?? [];
                                @endphp
                                
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="dashboard_layout[show_hero]" value="1" {{ ($layout['show_hero'] ?? true) ? 'checked' : '' }}> Show Server Hero (Large Header)
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="dashboard_layout[show_graphs]" value="1" {{ ($layout['show_graphs'] ?? true) ? 'checked' : '' }}> Show Performance Graphs
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="dashboard_layout[show_cpu]" value="1" {{ ($layout['show_cpu'] ?? true) ? 'checked' : '' }}> Show CPU Card
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="dashboard_layout[show_memory]" value="1" {{ ($layout['show_memory'] ?? true) ? 'checked' : '' }}> Show Memory Card
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="dashboard_layout[show_disk]" value="1" {{ ($layout['show_disk'] ?? true) ? 'checked' : '' }}> Show Disk Card
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Save Customizations</button>
                    <a href="{{ route('admin.index') }}" class="btn btn-default pull-right">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    <script>
        // Live preview of hex codes
        $('input[type="color"]').on('input', function() {
            var id = $(this).attr('id');
            $('#preview_' + id).text($(this).val());
            $('#preview_' + id).css('background-color', $(this).val());
            $('#preview_' + id).css('color', getContrastYIQ($(this).val()));
            
            // Magic Preview Variables (Optional - could add live CSS variable injection here)
        });

        function getContrastYIQ(hexcolor){
            hexcolor = hexcolor.replace("#", "");
            var r = parseInt(hexcolor.substr(0,2),16);
            var g = parseInt(hexcolor.substr(2,2),16);
            var b = parseInt(hexcolor.substr(4,2),16);
            var yiq = ((r*299)+(g*587)+(b*114))/1000;
            return (yiq >= 128) ? 'black' : 'white';
        }

        function applyPreset(preset) {
            const presets = {
                'purple': { 'primary_color': '#6366f1', 'secondary_color': '#3b82f6', 'background_color': '#0f0f18', 'gradient_start': '#6366f1', 'gradient_end': '#3b82f6', 'text_color': '#ffffff' },
                'blue': { 'primary_color': '#3b82f6', 'secondary_color': '#0ea5e9', 'background_color': '#0f172a', 'gradient_start': '#3b82f6', 'gradient_end': '#0ea5e9', 'text_color': '#ffffff' },
                'green': { 'primary_color': '#10b981', 'secondary_color': '#059669', 'background_color': '#064e3b', 'gradient_start': '#10b981', 'gradient_end': '#059669', 'text_color': '#ffffff' },
                'orange': { 'primary_color': '#f59e0b', 'secondary_color': '#d97706', 'background_color': '#451a03', 'gradient_start': '#f59e0b', 'gradient_end': '#d97706', 'text_color': '#ffffff' },
                'red': { 'primary_color': '#ef4444', 'secondary_color': '#b91c1c', 'background_color': '#450a0a', 'gradient_start': '#ef4444', 'gradient_end': '#b91c1c', 'text_color': '#ffffff' },
            };

            const colors = presets[preset];
            if (colors) {
                for (const [key, value] of Object.entries(colors)) {
                    $('#' + key).val(value);
                    $('#preview_' + key).text(value);
                }
                alert('Preset applied! Click Save to confirm.');
            }
        }
    </script>
@endsection
