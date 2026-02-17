<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained()->onDelete('set null');
            $table->string('username', 50)->unique();
            $table->string('password', 50);
            $table->string('profile', 100)->nullable();
            $table->string('speed_limit', 50)->nullable();
            $table->integer('validity_days')->nullable();
            $table->enum('status', ['available', 'assigned', 'expired'])->default('available');
            $table->foreignId('assigned_to_request_id')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('created_in_mikrotik_at')->nullable();
            $table->timestamps();
            
            $table->index(['router_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_users');
    }
};
