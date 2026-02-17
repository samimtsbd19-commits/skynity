<?php

namespace Database\Seeders;

use App\Models\CaptivePortalTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user if not exists
        if (!User::where('email', 'admin@skynity.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@skynity.com',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]);
        }

        // Create default captive portal template if not exists
        if (CaptivePortalTemplate::count() === 0) {
            CaptivePortalTemplate::create([
                'name' => 'ডিফল্ট টেমপ্লেট',
                'background_color' => '#1e3a5f',
                'primary_color' => '#3b82f6',
                'text_color' => '#ffffff',
                'welcome_title' => 'SKYNITY WiFi তে স্বাগতম!',
                'welcome_message' => 'আপনার পছন্দের প্যাকেজ সিলেক্ট করুন এবং হাই-স্পিড ইন্টারনেট উপভোগ করুন।',
                'footer_text' => 'SKYNITY - Fast & Reliable WiFi',
                'terms_conditions' => "1. এই সার্ভিস ব্যবহার করে আপনি আমাদের নীতিমালা মেনে নিচ্ছেন।\n2. অবৈধ কাজে ব্যবহার নিষিদ্ধ।\n3. প্যাকেজের মেয়াদ শেষ হলে সংযোগ স্বয়ংক্রিয়ভাবে বন্ধ হবে।",
                'payment_methods' => ['bkash', 'nagad', 'rocket', 'cash'],
                'show_packages' => true,
                'require_phone' => true,
                'require_email' => false,
                'is_active' => true,
                'is_default' => true,
            ]);
        }
    }
}
