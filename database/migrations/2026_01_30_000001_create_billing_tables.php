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
        // User wallets - stores coin balance
        Schema::create('billing_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->bigInteger('coins')->default(0);
            $table->bigInteger('total_earned')->default(0);
            $table->bigInteger('total_spent')->default(0);
            $table->timestamp('last_daily_claim')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Server plans with pricing
        Schema::create('billing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('memory'); // MB
            $table->integer('disk'); // MB
            $table->integer('cpu'); // Percent
            $table->integer('databases')->default(1);
            $table->integer('backups')->default(1);
            $table->integer('allocations')->default(1);
            $table->integer('coins_per_minute')->default(1);
            $table->integer('creation_cost')->default(0); // One-time cost to create
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Transaction history
        Schema::create('billing_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->enum('type', ['credit', 'debit']);
            $table->bigInteger('amount');
            $table->bigInteger('balance_after');
            $table->string('source'); // ad, code, daily, server, admin, referral
            $table->string('reference_id')->nullable(); // server_id, code_id, etc
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'created_at']);
        });

        // Redeem codes
        Schema::create('billing_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('coins');
            $table->integer('max_uses')->default(1);
            $table->integer('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Code usage tracking
        Schema::create('billing_code_uses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('code_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();

            $table->foreign('code_id')->references('id')->on('billing_codes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['code_id', 'user_id']); // Each user can only use a code once
        });

        // Ad views tracking (for cooldown)
        Schema::create('billing_ad_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('ad_type')->default('video'); // video, banner, etc
            $table->integer('coins_earned');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'created_at']);
        });

        // Server billing tracking
        Schema::create('billing_server_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('server_id');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('plan_id');
            $table->timestamp('billing_started_at')->nullable();
            $table->timestamp('last_billed_at')->nullable();
            $table->bigInteger('total_coins_spent')->default(0);
            $table->bigInteger('total_minutes')->default(0);
            $table->timestamps();

            $table->foreign('server_id')->references('id')->on('servers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('billing_plans');
        });

        // Billing settings
        Schema::create('billing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('billing_settings')->insert([
            ['key' => 'coins_per_ad', 'value' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ad_cooldown_seconds', 'value' => '60', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'daily_reward_coins', 'value' => '50', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_servers_per_user', 'value' => '2', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'new_user_bonus', 'value' => '100', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert default plans
        DB::table('billing_plans')->insert([
            [
                'name' => 'Starter',
                'description' => 'Perfect for small Minecraft servers',
                'memory' => 1024,
                'disk' => 5120,
                'cpu' => 50,
                'databases' => 1,
                'backups' => 1,
                'allocations' => 1,
                'coins_per_minute' => 1,
                'creation_cost' => 0,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Standard',
                'description' => 'Great for medium-sized servers',
                'memory' => 2048,
                'disk' => 10240,
                'cpu' => 100,
                'databases' => 2,
                'backups' => 2,
                'allocations' => 2,
                'coins_per_minute' => 2,
                'creation_cost' => 50,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium',
                'description' => 'For larger communities',
                'memory' => 4096,
                'disk' => 20480,
                'cpu' => 200,
                'databases' => 3,
                'backups' => 3,
                'allocations' => 3,
                'coins_per_minute' => 4,
                'creation_cost' => 100,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_server_usage');
        Schema::dropIfExists('billing_ad_views');
        Schema::dropIfExists('billing_code_uses');
        Schema::dropIfExists('billing_codes');
        Schema::dropIfExists('billing_transactions');
        Schema::dropIfExists('billing_plans');
        Schema::dropIfExists('billing_wallets');
        Schema::dropIfExists('billing_settings');
    }
};
