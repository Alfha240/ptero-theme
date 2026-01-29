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
            $table->string('primary_color')->nullable()->after('theme_enabled');
            $table->string('secondary_color')->nullable()->after('primary_color');
            $table->string('background_color')->nullable()->after('secondary_color');
            $table->string('text_color')->nullable()->after('background_color');
            $table->string('gradient_start')->nullable()->after('text_color');
            $table->string('gradient_end')->nullable()->after('gradient_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->dropColumn([
                'primary_color',
                'secondary_color',
                'background_color',
                'text_color',
                'gradient_start',
                'gradient_end',
            ]);
        });
    }
};
