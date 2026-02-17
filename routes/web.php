<?php

use App\Http\Controllers\CaptivePortalController;
use App\Livewire\Dashboard;
use App\Livewire\RouterManager;
use App\Livewire\PackageManager;
use App\Livewire\VoucherGenerator;
use App\Livewire\VoucherList;
use App\Livewire\SalesReport;
use App\Livewire\ActiveSessions;
use App\Livewire\HotspotUsers;
use App\Livewire\IpBindingManager;
use App\Livewire\ProfileSync;
use App\Livewire\TrafficMonitor;
use App\Livewire\Settings;
use App\Livewire\HotspotRequestManager;
use App\Livewire\TemplateEditor;
use App\Livewire\CustomerDashboard;
use App\Livewire\StockUserManager;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WireGuardController;

/*
|--------------------------------------------------------------------------
| SKYNITY WiFi Management Routes
|--------------------------------------------------------------------------
*/

// =========================================
// CAPTIVE PORTAL ROUTES (PUBLIC - NO AUTH)
// Rate limited to prevent abuse
// =========================================
Route::prefix('captive')->name('captive.')->middleware('throttle:60,1')->group(function () {
    Route::get('/', [CaptivePortalController::class, 'index'])->name('index');
    Route::get('/select/{package}', [CaptivePortalController::class, 'selectPackage'])->name('select');
    Route::post('/submit', [CaptivePortalController::class, 'submitPayment'])->middleware('throttle:10,1')->name('submit');
    Route::post('/trial', [CaptivePortalController::class, 'submitTrial'])->middleware('throttle:5,1')->name('trial');
    Route::get('/payment', [CaptivePortalController::class, 'showPayment'])->name('payment');
    Route::post('/custom-submit', [CaptivePortalController::class, 'submitCustomPayment'])->middleware('throttle:10,1')->name('custom.submit');
    Route::get('/status/{requestId}', [CaptivePortalController::class, 'checkStatus'])->name('status');
    Route::get('/activated/{id}', [CaptivePortalController::class, 'activated'])->name('activated');
    Route::get('/preview/{template}', function ($templateId) {
        $template = \App\Models\CaptivePortalTemplate::findOrFail($templateId);
        $router = $template->router ?? \App\Models\Router::first();
        $packages = $router ? \App\Models\Package::where('router_id', $router->id)->where('is_active', true)->get() : collect();
        return view('captive.index', [
            'template' => $template,
            'router' => $router ?? (object)['name' => 'Demo Router', 'id' => 0],
            'packages' => $packages,
            'clientMac' => '',
            'clientIp' => request()->ip(),
        ]);
    })->name('preview');
});

// Guest Routes (Rate limited for brute force protection)
Route::middleware(['guest', 'throttle:5,1'])->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function () {
        $credentials = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials, request()->boolean('remember'))) {
            request()->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ]);
    });
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', Dashboard::class)->name('dashboard');
    
    // Routers
    Route::get('/routers', RouterManager::class)->name('routers');
    
    // Packages
    Route::get('/packages', PackageManager::class)->name('packages');
    
    // Vouchers
    Route::get('/vouchers/generate', VoucherGenerator::class)->name('vouchers.generate');
    Route::get('/vouchers', VoucherList::class)->name('vouchers.list');
    Route::get('/vouchers/print', function () {
        $ids = explode(',', request('ids'));
        $vouchers = \App\Models\Voucher::whereIn('id', $ids)->with('package')->get();
        return view('vouchers.print', compact('vouchers'));
    })->name('vouchers.print');
    
    // MikroTik Live Management (Mikhmon-like features)
    Route::get('/sessions', ActiveSessions::class)->name('sessions');
    Route::get('/hotspot-users', HotspotUsers::class)->name('hotspot.users');
    Route::get('/ip-binding', IpBindingManager::class)->name('ip.binding');
    Route::get('/profiles', ProfileSync::class)->name('profiles');
    Route::get('/traffic', TrafficMonitor::class)->name('traffic');
    Route::get('/stock-users', StockUserManager::class)->name('stock.users');
    
    // Hotspot Requests Management (Admin)
    Route::get('/hotspot-requests', HotspotRequestManager::class)->name('hotspot.requests');
    
    // Captive Portal Template Editor
    Route::get('/templates', TemplateEditor::class)->name('templates');
    
    // Reports
    Route::get('/reports/sales', SalesReport::class)->name('reports.sales');
    
    // Settings
    Route::get('/settings', Settings::class)->name('settings');
    
    Route::post('/wireguard/peers', [WireGuardController::class, 'addPeer'])->name('wireguard.peers.add');
    Route::delete('/wireguard/peers/{id}', [WireGuardController::class, 'removePeer'])->name('wireguard.peers.remove');
    Route::get('/wireguard/peers/{id}/config', [WireGuardController::class, 'config'])->name('wireguard.peers.config');
    
    // Logout
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});

// =========================================
// CUSTOMER ROUTES (AUTH - CUSTOMER ROLE)
// =========================================
Route::middleware('auth')->prefix('my')->name('customer.')->group(function () {
    Route::get('/dashboard', CustomerDashboard::class)->name('dashboard');
});
