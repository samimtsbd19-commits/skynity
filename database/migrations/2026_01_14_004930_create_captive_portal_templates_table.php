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
        Schema::create('captive_portal_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->string('background_image')->nullable();
            $table->string('background_color')->default('#1e3a5f');
            $table->string('primary_color')->default('#3b82f6');
            $table->string('text_color')->default('#ffffff');
            $table->text('welcome_title')->nullable();
            $table->text('welcome_message')->nullable();
            $table->text('footer_text')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('custom_css')->nullable();
            $table->text('custom_js')->nullable();
            $table->json('payment_methods')->nullable();
            $table->boolean('show_packages')->default(true);
            $table->boolean('require_phone')->default(true);
            $table->boolean('require_email')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('captive_portal_templates');
    }
};
