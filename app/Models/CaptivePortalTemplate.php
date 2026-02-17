<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaptivePortalTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'router_id',
        'name',
        'logo_path',
        'background_image',
        'background_color',
        'primary_color',
        'text_color',
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
        'welcome_title',
        'welcome_message',
        'footer_text',
        'terms_conditions',
        'custom_css',
        'custom_js',
        'payment_methods',
        'show_packages',
        'require_phone',
        'require_email',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'payment_methods' => 'array',
        'show_packages' => 'boolean',
        'require_phone' => 'boolean',
        'require_email' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'package_card_shadow' => 'boolean',
        'package_grid_sm' => 'integer',
        'package_grid_md' => 'integer',
        'package_grid_lg' => 'integer',
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public static function getDefault()
    {
        return self::where('is_default', true)->first() ?? self::first();
    }

    public static function getForRouter(int $routerId)
    {
        return self::where('router_id', $routerId)->where('is_active', true)->first() 
            ?? self::getDefault();
    }

    public function setAsDefault()
    {
        self::query()->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    public function getBackgroundImageUrlAttribute()
    {
        return $this->background_image ? asset('storage/' . $this->background_image) : null;
    }
}
