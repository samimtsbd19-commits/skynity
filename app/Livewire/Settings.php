<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Settings extends Component
{
    public $settings = [];
    
    // General Settings
    public $appName = 'SKYNITY WiFi';
    public $currency = '৳';
    public $timezone = 'Asia/Dhaka';
    public $language = 'bn';

    // Voucher Settings
    public $voucherPrefix = '';
    public $voucherLength = 6;
    public $passwordLength = 6;
    public $usernameType = 'random'; // random, sequential
    public $passwordType = 'random'; // random, same_as_username

    // Print Settings
    public $printTemplate = 'default';
    public $printColumns = 3;
    public $showQrCode = true;
    public $showPrice = true;
    public $shopName = '';
    public $shopAddress = '';
    public $shopPhone = '';
    public $footerText = 'ধন্যবাদ! আবার আসবেন।';

    // Notification Settings
    public $enableEmail = false;
    public $enableSms = false;
    public $smsApiUrl = '';
    public $smsApiKey = '';

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();

        $this->appName = $settings['app_name'] ?? 'SKYNITY WiFi';
        $this->currency = $settings['currency'] ?? '৳';
        $this->timezone = $settings['timezone'] ?? 'Asia/Dhaka';
        $this->language = $settings['language'] ?? 'bn';

        $this->voucherPrefix = $settings['voucher_prefix'] ?? '';
        $this->voucherLength = (int)($settings['voucher_length'] ?? 6);
        $this->passwordLength = (int)($settings['password_length'] ?? 6);
        $this->usernameType = $settings['username_type'] ?? 'random';
        $this->passwordType = $settings['password_type'] ?? 'random';

        $this->printTemplate = $settings['print_template'] ?? 'default';
        $this->printColumns = (int)($settings['print_columns'] ?? 3);
        $this->showQrCode = ($settings['show_qr_code'] ?? 'true') === 'true';
        $this->showPrice = ($settings['show_price'] ?? 'true') === 'true';
        $this->shopName = $settings['shop_name'] ?? '';
        $this->shopAddress = $settings['shop_address'] ?? '';
        $this->shopPhone = $settings['shop_phone'] ?? '';
        $this->footerText = $settings['footer_text'] ?? 'ধন্যবাদ! আবার আসবেন।';

        $this->enableEmail = ($settings['enable_email'] ?? 'false') === 'true';
        $this->enableSms = ($settings['enable_sms'] ?? 'false') === 'true';
        $this->smsApiUrl = $settings['sms_api_url'] ?? '';
        $this->smsApiKey = $settings['sms_api_key'] ?? '';
    }

    public function saveGeneral()
    {
        $this->saveSetting('app_name', $this->appName);
        $this->saveSetting('currency', $this->currency);
        $this->saveSetting('timezone', $this->timezone);
        $this->saveSetting('language', $this->language);

        session()->flash('message', 'সাধারণ সেটিংস সংরক্ষিত হয়েছে!');
    }

    public function saveVoucher()
    {
        $this->saveSetting('voucher_prefix', $this->voucherPrefix);
        $this->saveSetting('voucher_length', (string)$this->voucherLength);
        $this->saveSetting('password_length', (string)$this->passwordLength);
        $this->saveSetting('username_type', $this->usernameType);
        $this->saveSetting('password_type', $this->passwordType);

        session()->flash('message', 'ভাউচার সেটিংস সংরক্ষিত হয়েছে!');
    }

    public function savePrint()
    {
        $this->saveSetting('print_template', $this->printTemplate);
        $this->saveSetting('print_columns', (string)$this->printColumns);
        $this->saveSetting('show_qr_code', $this->showQrCode ? 'true' : 'false');
        $this->saveSetting('show_price', $this->showPrice ? 'true' : 'false');
        $this->saveSetting('shop_name', $this->shopName);
        $this->saveSetting('shop_address', $this->shopAddress);
        $this->saveSetting('shop_phone', $this->shopPhone);
        $this->saveSetting('footer_text', $this->footerText);

        session()->flash('message', 'প্রিন্ট সেটিংস সংরক্ষিত হয়েছে!');
    }

    public function saveNotification()
    {
        $this->saveSetting('enable_email', $this->enableEmail ? 'true' : 'false');
        $this->saveSetting('enable_sms', $this->enableSms ? 'true' : 'false');
        $this->saveSetting('sms_api_url', $this->smsApiUrl);
        $this->saveSetting('sms_api_key', $this->smsApiKey);

        session()->flash('message', 'নোটিফিকেশন সেটিংস সংরক্ষিত হয়েছে!');
    }

    private function saveSetting($key, $value)
    {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => now()]
        );
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
