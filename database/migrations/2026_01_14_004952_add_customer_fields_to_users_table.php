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
        Schema::table('users', function (Blueprint $table) {
            // Modify existing role enum to include customer
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->enum('role', ['admin', 'operator', 'customer'])->default('customer')->after('phone');
            $table->string('hotspot_username')->nullable()->after('role');
            $table->string('hotspot_password')->nullable()->after('hotspot_username');
            $table->foreignId('router_id')->nullable()->after('hotspot_password')->constrained()->onDelete('set null');
            $table->foreignId('package_id')->nullable()->after('router_id')->constrained()->onDelete('set null');
            $table->timestamp('subscription_expires_at')->nullable()->after('package_id');
            $table->string('mac_address')->nullable()->after('subscription_expires_at');
            $table->decimal('balance', 10, 2)->default(0)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['router_id']);
            $table->dropForeign(['package_id']);
            $table->dropColumn([
                'phone', 'hotspot_username', 'hotspot_password',
                'router_id', 'package_id', 'subscription_expires_at',
                'mac_address', 'balance', 'role'
            ]);
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'operator'])->default('operator')->after('password');
        });
    }
};
