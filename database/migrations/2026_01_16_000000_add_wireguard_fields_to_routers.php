<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->string('wireguard_interface')->nullable()->after('hotspot_url');
            $table->string('wireguard_endpoint')->nullable()->after('wireguard_interface');
            $table->string('wireguard_server_public_key')->nullable()->after('wireguard_endpoint');
            $table->string('wireguard_subnet')->nullable()->after('wireguard_server_public_key');
            $table->string('wireguard_dns')->nullable()->after('wireguard_subnet');
            $table->integer('wireguard_keepalive')->nullable()->after('wireguard_dns');
        });
    }

    public function down(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->dropColumn([
                'wireguard_interface',
                'wireguard_endpoint',
                'wireguard_server_public_key',
                'wireguard_subnet',
                'wireguard_dns',
                'wireguard_keepalive',
            ]);
        });
    }
};
