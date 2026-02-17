<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // রাউটার টেবিল
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address');
            $table->integer('port')->default(8728);
            $table->string('username');
            $table->string('password');
            $table->string('hotspot_name')->nullable();
            $table->string('dns_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamps();
        });

        // প্যাকেজ টেবিল (হটস্পট প্রোফাইল)
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('mikrotik_profile'); // MikroTik এ প্রোফাইল নাম
            $table->string('validity')->nullable(); // 1h, 1d, 7d, 30d
            $table->string('data_limit')->nullable(); // 1G, 5G, unlimited
            $table->string('speed_limit')->nullable(); // 2M/2M
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ভাউচার টেবিল
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('batch_code')->nullable(); // জেনারেশন ব্যাচ
            $table->enum('status', ['unused', 'used', 'expired', 'disabled'])->default('unused');
            $table->timestamp('first_login_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('used_by_mac')->nullable();
            $table->decimal('sold_price', 10, 2)->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // বিক্রয় রেকর্ড
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
            $table->foreignId('sold_by')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->default('cash'); // cash, bkash, nagad
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // অ্যাক্টিভ সেশন লগ
        Schema::create('session_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained()->onDelete('cascade');
            $table->string('username');
            $table->string('mac_address')->nullable();
            $table->string('ip_address')->nullable();
            $table->bigInteger('bytes_in')->default(0);
            $table->bigInteger('bytes_out')->default(0);
            $table->integer('uptime_seconds')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });

        // সেটিংস
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('session_logs');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('routers');
    }
};
