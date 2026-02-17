<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotspot_requests', function (Blueprint $table) {
            // Trial fields
            $table->boolean('is_trial')->default(false)->after('notes');
            $table->integer('trial_days')->nullable()->after('is_trial');
            $table->string('trial_speed', 50)->nullable()->after('trial_days');
            
            // Custom package fields
            $table->boolean('is_custom')->default(false)->after('trial_speed');
            $table->string('custom_speed', 50)->nullable()->after('is_custom');
            $table->integer('custom_days')->nullable()->after('custom_speed');
            $table->integer('custom_devices')->default(1)->after('custom_days');
        });
    }

    public function down(): void
    {
        Schema::table('hotspot_requests', function (Blueprint $table) {
            $table->dropColumn([
                'is_trial',
                'trial_days',
                'trial_speed',
                'is_custom',
                'custom_speed',
                'custom_days',
                'custom_devices',
            ]);
        });
    }
};
