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
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Custom CSS</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="theme_enabled" class="control-label">Enable Custom Theme</label>
                        <div>
                            <input type="checkbox" name="theme_enabled" id="theme_enabled" {{ $setting->theme_enabled ? 'checked' : '' }}>
                            <span class="help-block">Toggle this to enable or disable the custom CSS globally.</span>
                        </div>
                    </div>

                    {{-- Colors Section --}}
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
                                <label for="background_color" class="control-label">Background Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control" name="background_color" id="background_color" value="{{ $setting->background_color ?? '#0f0f18' }}" style="height: 40px;">
                                    <span class="input-group-addon code-preview" id="preview_background_color">{{ $setting->background_color ?? '#0f0f18' }}</span>
                                </div>
                            </div>
                        </div>
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
                    </div>

                    <div class="row">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="text_color" class="control-label">Text Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control" name="text_color" id="text_color" value="{{ $setting->text_color ?? '#ffffff' }}" style="height: 40px;">
                                    <span class="input-group-addon code-preview" id="preview_text_color">{{ $setting->text_color ?? '#ffffff' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                        <label for="custom_css" class="control-label">CSS Editor</label>
                        <p class="text-muted small">Enter your custom CSS below. Variables like <code>--primary-color</code> can be used if supported by your base theme.</p>
                        <div id="editor_container" style="height: 500px; border: 1px solid #ddd;"></div>
                        <textarea name="custom_css" id="custom_css" class="hidden">{{ $setting->custom_css }}</textarea>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    <!-- Loading Monaco Editor from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.34.1/min/vs/loader.min.js"></script>
    <script>
        require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.34.1/min/vs' }});
        require(['vs/editor/editor.main'], function() {
            var editor = monaco.editor.create(document.getElementById('editor_container'), {
                value: document.getElementById('custom_css').value,
                language: 'css',
                theme: 'vs-dark',
                automaticLayout: true
            });

            // Sync content on form submit
            $('form').on('submit', function() {
                $('#custom_css').val(editor.getValue());
            });

            // Live preview of hex codes
            $('input[type="color"]').on('input', function() {
                $('#preview_' + $(this).attr('id')).text($(this).val());
            });
        });

        function applyPreset(preset) {
            const presets = {
                'purple': {
                    'primary_color': '#6366f1',
                    'secondary_color': '#3b82f6',
                    'background_color': '#0f0f18',
                    'gradient_start': '#6366f1',
                    'gradient_end': '#3b82f6',
                    'text_color': '#ffffff'
                },
                'blue': {
                    'primary_color': '#3b82f6',
                    'secondary_color': '#0ea5e9',
                    'background_color': '#0f172a',
                    'gradient_start': '#3b82f6',
                    'gradient_end': '#0ea5e9',
                    'text_color': '#ffffff'
                },
                'green': {
                    'primary_color': '#10b981',
                    'secondary_color': '#059669',
                    'background_color': '#064e3b',
                    'gradient_start': '#10b981',
                    'gradient_end': '#059669',
                    'text_color': '#ffffff'
                },
                'orange': {
                    'primary_color': '#f59e0b',
                    'secondary_color': '#d97706',
                    'background_color': '#451a03',
                    'gradient_start': '#f59e0b',
                    'gradient_end': '#d97706',
                    'text_color': '#ffffff'
                },
                'red': {
                    'primary_color': '#ef4444',
                    'secondary_color': '#b91c1c',
                    'background_color': '#450a0a',
                    'gradient_start': '#ef4444',
                    'gradient_end': '#b91c1c',
                    'text_color': '#ffffff'
                },
            };

            const colors = presets[preset];
            if (colors) {
                for (const [key, value] of Object.entries(colors)) {
                    $('#' + key).val(value);
                    $('#preview_' + key).text(value);
                }
            }
        }
    </script>
@endsection
