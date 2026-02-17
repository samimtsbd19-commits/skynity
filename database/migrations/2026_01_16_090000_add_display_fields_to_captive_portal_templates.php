<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('captive_portal_templates', function (Blueprint $table) {
            $table->string('font_family')->nullable()->after('text_color');
            $table->unsignedInteger('base_font_size')->nullable()->after('font_family'); // px
            $table->unsignedInteger('heading_font_size')->nullable()->after('base_font_size'); // px
            $table->unsignedInteger('button_radius')->nullable()->after('heading_font_size'); // px
            $table->unsignedInteger('package_card_radius')->nullable()->after('button_radius'); // px
            $table->boolean('package_card_shadow')->default(true)->after('package_card_radius');
            $table->unsignedTinyInteger('package_grid_sm')->default(2)->after('package_card_shadow');
            $table->unsignedTinyInteger('package_grid_md')->default(2)->after('package_grid_sm');
            $table->unsignedTinyInteger('package_grid_lg')->default(3)->after('package_grid_md');
            $table->string('package_card_bg')->nullable()->after('package_grid_lg');
            $table->string('package_card_text')->nullable()->after('package_card_bg');
            $table->string('cta_button_text')->nullable()->after('package_card_text');
            $table->string('cta_button_color')->nullable()->after('cta_button_text');
            $table->string('cta_button_text_color')->nullable()->after('cta_button_color');
        });
    }

    public function down(): void
    {
        Schema::table('captive_portal_templates', function (Blueprint $table) {
            $table->dropColumn([
                'font_family',
                'base_font_size',
                'heading_font_size',
                'button_radius',
                'package_card_radius',
                'package_card_shadow',
                'package_grid_sm',
                'package_grid_md',
                'package_grid_lg',
                'package_card_bg',
                'package_card_text',
                'cta_button_text',
                'cta_button_color',
                'cta_button_text_color',
            ]);
        });
    }
};

