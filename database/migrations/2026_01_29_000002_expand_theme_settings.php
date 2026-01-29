<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->string('card_style')->default('solid')->after('gradient_end');
            $table->string('sidebar_style')->default('full')->after('card_style');
            $table->string('border_radius')->default('8px')->after('sidebar_style');
            $table->string('blur_intensity')->default('10px')->after('border_radius');
            $table->string('app_name')->nullable()->after('blur_intensity');
            $table->string('logo_url')->nullable()->after('app_name');
            $table->string('favicon_url')->nullable()->after('logo_url');
            $table->string('login_background_url')->nullable()->after('favicon_url');
            $table->json('dashboard_layout')->nullable()->after('login_background_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
             $table->dropColumn([
                'card_style',
                'sidebar_style',
                'border_radius',
                'blur_intensity',
                'app_name',
                'logo_url',
                'favicon_url',
                'login_background_url',
                'dashboard_layout',
             ]);
        });
    }
};
