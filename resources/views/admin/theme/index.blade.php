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
                    
                    <div class="form-group">
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
        });
    </script>
@endsection
