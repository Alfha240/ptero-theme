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
<style>
    /* Theme Customizer Sidebar Layout */
    .theme-customizer-wrapper {
        display: flex;
        min-height: 600px;
        background: #1a1c2e;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }
    
    .theme-sidebar {
        width: 240px;
        background: #252841;
        border-right: 1px solid #2d3152;
        padding: 20px 0;
    }
    
    .theme-sidebar-item {
        padding: 15px 25px;
        color: #a8b3cf;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        border-left: 3px solid transparent;
    }
    
    .theme-sidebar-item:hover {
        background: rgba(99, 102, 241, 0.1);
        color: #fff;
    }
    
    .theme-sidebar-item.active {
        background: rgba(99, 102, 241, 0.15);
        color: #fff;
        border-left-color: #6366f1;
    }
    
    .theme-sidebar-item i {
        font-size: 18px;
        width: 20px;
        text-align: center;
    }
    
    .theme-content {
        flex: 1;
        padding: 30px;
        background: #1a1c2e;
        overflow-y: auto;
        max-height: 700px;
    }
    
    .theme-section {
        display: none;
    }
    
    .theme-section.active {
        display: block;
    }
    
    .theme-content .form-group label {
        color: #e2e8f0;
        font-weight: 500;
        margin-bottom: 8px;
    }
    
    .theme-content .form-control {
        background: #252841;
        border: 1px solid #2d3152;
        color: #fff;
        padding: 10px 15px;
    }
    
    .theme-content .form-control:focus {
        background: #2d3152;
        border-color: #6366f1;
        color: #fff;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    .theme-content select.form-control {
        background: #252841;
        color: #fff;
    }
    
    .theme-content .text-muted,
    .theme-content .help-block {
        color: #94a3b8 !important;
    }
    
    .preset-card {
        background: #252841;
        border: 2px solid #2d3152 !important;
        border-radius: 8px !important;
        padding: 15px !important;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 110px !important;
    }
    
    .preset-card:hover {
        border-color: #6366f1 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }
    
    .preset-card small {
        color: #a8b3cf;
        font-weight: 500;
    }
    
    .code-preview {
        background: #252841 !important;
        color: #fff !important;
        border: 1px solid #2d3152 !important;
        font-family: 'Courier New', monospace;
        font-size: 12px;
    }
    
    .input-group-addon {
        background: #252841;
        border-color: #2d3152;
        color: #fff;
    }
    
    .theme-footer {
        padding: 20px 30px;
        background: #252841;
        border-top: 1px solid #2d3152;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6366f1, #3b82f6);
        border: none;
        padding: 10px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
    }
    
    .btn-default {
        background: #2d3152;
        border: 1px solid #3d4266;
        color: #a8b3cf;
    }
    
    .btn-default:hover {
        background: #3d4266;
        color: #fff;
    }
    
    .checkbox label {
        color: #e2e8f0;
        font-weight: 400;
    }
    
    input[type="checkbox"] {
        margin-right: 8px;
    }
    
    .section-title {
        color: #fff;
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #2d3152;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <form action="{{ route('admin.theme') }}" method="POST">
            @csrf
            
            <div class="theme-customizer-wrapper">
                <!-- Left Sidebar Navigation -->
                <div class="theme-sidebar">
                    <div class="theme-sidebar-item active" data-section="colors">
                        <i class="fa fa-palette"></i>
                        <span>Theme Colors</span>
                    </div>
                    <div class="theme-sidebar-item" data-section="design">
                        <i class="fa fa-paint-brush"></i>
                        <span>Design & Layout</span>
                    </div>
                    <div class="theme-sidebar-item" data-section="branding">
                        <i class="fa fa-tag"></i>
                        <span>Branding</span>
                    </div>
                    <div class="theme-sidebar-item" data-section="dashboard">
                        <i class="fa fa-th-large"></i>
                        <span>Dashboard Elements</span>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div style="flex: 1; display: flex; flex-direction: column;">
                    <div class="theme-content">
                        
                        {{-- SECTION 1: COLORS --}}
                        <div class="theme-section active" id="section-colors">
                            <h3 class="section-title">Theme Colors</h3>
                            
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label for="theme_enabled" class="control-label">
                                            <input type="checkbox" name="theme_enabled" id="theme_enabled" {{ $setting->theme_enabled ? 'checked' : '' }}>
                                            Enable Custom Theme
                                        </label>
                                        <p class="help-block">Toggle this to enable or disable the custom CSS globally.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <label class="control-label">Quick Presets</label>
                                    <div style="display: flex; gap: 12px; margin-bottom: 30px; margin-top: 10px; flex-wrap: wrap;">
                                        <div class="preset-card" onclick="applyPreset('purple')">
                                            <div style="width: 30px; height: 30px; background: #6366f1; border-radius: 8px; margin: 0 auto 8px;"></div>
                                            <small>Purple</small>
                                        </div>
                                        <div class="preset-card" onclick="applyPreset('blue')">
                                            <div style="width: 30px; height: 30px; background: #3b82f6; border-radius: 8px; margin: 0 auto 8px;"></div>
                                            <small>Blue</small>
                                        </div>
                                        <div class="preset-card" onclick="applyPreset('green')">
                                            <div style="width: 30px; height: 30px; background: #10b981; border-radius: 8px; margin: 0 auto 8px;"></div>
                                            <small>Green</small>
                                        </div>
                                        <div class="preset-card" onclick="applyPreset('orange')">
                                            <div style="width: 30px; height: 30px; background: #f59e0b; border-radius: 8px; margin: 0 auto 8px;"></div>
                                            <small>Orange</small>
                                        </div>
                                        <div class="preset-card" onclick="applyPreset('red')">
                                            <div style="width: 30px; height: 30px; background: #ef4444; border-radius: 8px; margin: 0 auto 8px;"></div>
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
                                            <input type="color" class="form-control" name="primary_color" id="primary_color" value="{{ $setting->primary_color ?? '#6366f1' }}" style="height: 45px;">
                                            <span class="input-group-addon code-preview" id="preview_primary_color">{{ $setting->primary_color ?? '#6366f1' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="secondary_color" class="control-label">Secondary Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control" name="secondary_color" id="secondary_color" value="{{ $setting->secondary_color ?? '#3b82f6' }}" style="height: 45px;">
                                            <span class="input-group-addon code-preview" id="preview_secondary_color">{{ $setting->secondary_color ?? '#3b82f6' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="background_color" class="control-label">Background Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control" name="background_color" id="background_color" value="{{ $setting->background_color ?? '#0f0f18' }}" style="height: 45px;">
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
                                            <input type="color" class="form-control" name="text_color" id="text_color" value="{{ $setting->text_color ?? '#ffffff' }}" style="height: 45px;">
                                            <span class="input-group-addon code-preview" id="preview_text_color">{{ $setting->text_color ?? '#ffffff' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gradient_start" class="control-label">Gradient Start</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control" name="gradient_start" id="gradient_start" value="{{ $setting->gradient_start ?? '#6366f1' }}" style="height: 45px;">
                                            <span class="input-group-addon code-preview" id="preview_gradient_start">{{ $setting->gradient_start ?? '#6366f1' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gradient_end" class="control-label">Gradient End</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control" name="gradient_end" id="gradient_end" value="{{ $setting->gradient_end ?? '#3b82f6' }}" style="height: 45px;">
                                            <span class="input-group-addon code-preview" id="preview_gradient_end">{{ $setting->gradient_end ?? '#3b82f6' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECTION 2: DESIGN --}}
                        <div class="theme-section" id="section-design">
                            <h3 class="section-title">Design & Layout</h3>
                            
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

                        {{-- SECTION 3: BRANDING --}}
                        <div class="theme-section" id="section-branding">
                            <h3 class="section-title">Branding</h3>
                            
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

                        {{-- SECTION 4: DASHBOARD ELEMENTS --}}
                        <div class="theme-section" id="section-dashboard">
                            <h3 class="section-title">Dashboard Elements</h3>
                            
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

                    <!-- Footer Actions -->
                    <div class="theme-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Save Customizations
                        </button>
                        <a href="{{ route('admin.index') }}" class="btn btn-default">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    <script>
        // Sidebar Navigation
        document.querySelectorAll('.theme-sidebar-item').forEach(item => {
            item.addEventListener('click', function() {
                // Remove active class from all items
                document.querySelectorAll('.theme-sidebar-item').forEach(i => i.classList.remove('active'));
                document.querySelectorAll('.theme-section').forEach(s => s.classList.remove('active'));
                
                // Add active class to clicked item
                this.classList.add('active');
                
                // Show corresponding section
                const section = this.dataset.section;
                document.getElementById('section-' + section).classList.add('active');
            });
        });

        // Live preview of hex codes
        $('input[type="color"]').on('input', function() {
            var id = $(this).attr('id');
            $('#preview_' + id).text($(this).val());
            $('#preview_' + id).css('background-color', $(this).val());
            $('#preview_' + id).css('color', getContrastYIQ($(this).val()));
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
                    $('#preview_' + key).css('background-color', value);
                    $('#preview_' + key).css('color', getContrastYIQ(value));
                }
                alert('Preset applied! Click Save to confirm.');
            }
        }
    </script>
@endsection
