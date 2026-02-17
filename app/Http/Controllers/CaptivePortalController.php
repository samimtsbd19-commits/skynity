<?php

namespace App\Http\Controllers;

use App\Models\CaptivePortalTemplate;
use App\Models\HotspotRequest;
use App\Models\Notification;
use App\Models\Package;
use App\Models\Router;
use Illuminate\Http\Request;

class CaptivePortalController extends Controller
{
    /**
     * Show the captive portal page for package selection
     */
    public function index(Request $request)
    {
        // Get router from request or use first active router
        $routerId = $request->query('router');
        $router = $routerId 
            ? Router::find($routerId) 
            : Router::where('is_active', true)->first();

        if (!$router) {
            return view('captive.no-router');
        }

        // Get template for this router
        $template = CaptivePortalTemplate::getForRouter($router->id);

        // Get active packages for this router
        $packages = Package::where('router_id', $router->id)
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        // Get client info
        $clientMac = $request->query('mac', $request->header('X-Client-Mac', ''));
        $clientIp = $request->ip();

        return view('captive.index', compact('router', 'template', 'packages', 'clientMac', 'clientIp'));
    }

    /**
     * Show package details
     */
    public function selectPackage(Request $request, Package $package)
    {
        $router = $package->router;
        $template = CaptivePortalTemplate::getForRouter($router->id);
        $clientMac = $request->query('mac', '');
        $clientIp = $request->ip();

        return view('captive.payment', compact('package', 'router', 'template', 'clientMac', 'clientIp'));
    }

    /**
     * Submit payment and create hotspot request
     */
    public function submitPayment(Request $request)
    {
        $validated = $request->validate([
            'router_id' => 'required|exists:routers,id',
            'package_id' => 'required|exists:packages,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'payment_method' => 'required|in:bkash,nagad,rocket,cash,other',
            'transaction_id' => 'nullable|string|max:100',
            'mac_address' => 'nullable|string|max:50',
            'ip_address' => 'nullable|string|max:50',
        ]);

        $package = Package::find($validated['package_id']);

        // Create hotspot request
        $hotspotRequest = HotspotRequest::create([
            'router_id' => $validated['router_id'],
            'package_id' => $validated['package_id'],
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? null,
            'mac_address' => $validated['mac_address'] ?? null,
            'ip_address' => $validated['ip_address'] ?? $request->ip(),
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'] ?? null,
            'amount' => $package->price,
            'status' => 'pending',
        ]);

        // Send notification to admins
        Notification::sendToAdmins(
            'hotspot_request',
            'নতুন হটস্পট রিকোয়েস্ট',
            "{$validated['customer_name']} ({$validated['customer_phone']}) - {$package->name} প্যাকেজের জন্য পেমেন্ট করেছে। Transaction: {$validated['transaction_id']}",
            [
                'request_id' => $hotspotRequest->id,
                'package_name' => $package->name,
                'amount' => $package->price,
            ]
        );

        $template = CaptivePortalTemplate::getForRouter($validated['router_id']);
        return view('captive.success', [
            'request' => $hotspotRequest,
            'package' => $package,
            'template' => $template,
        ]);
    }

    /**
     * Check request status (for AJAX polling)
     */
    public function checkStatus(Request $request, $requestId)
    {
        $hotspotRequest = HotspotRequest::find($requestId);

        if (!$hotspotRequest) {
            return response()->json(['status' => 'not_found'], 404);
        }

        $response = [
            'status' => $hotspotRequest->status,
            'message' => $this->getStatusMessage($hotspotRequest),
        ];

        if ($hotspotRequest->status === 'approved') {
            $response['voucher_code'] = $hotspotRequest->voucher_code;
            $response['redirect'] = route('captive.activated', ['id' => $requestId]);
        }

        return response()->json($response);
    }

    /**
     * Show activation success page with credentials
     */
    public function activated(Request $request, $id)
    {
        $hotspotRequest = HotspotRequest::with(['package', 'router', 'user'])->find($id);

        if (!$hotspotRequest || $hotspotRequest->status !== 'approved') {
            return redirect()->route('captive.index');
        }

        return view('captive.activated', [
            'request' => $hotspotRequest,
            'user' => $hotspotRequest->user,
            'template' => CaptivePortalTemplate::getForRouter($hotspotRequest->router->id),
        ]);
    }

    /**
     * Submit free trial request
     */
    public function submitTrial(Request $request)
    {
        $validated = $request->validate([
            'router_id' => 'required|exists:routers,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'mac_address' => 'nullable|string|max:50',
            'ip_address' => 'nullable|string|max:50',
        ]);

        // Check if phone already used for trial
        $existingTrial = HotspotRequest::where('customer_phone', $validated['customer_phone'])
            ->where('is_trial', true)
            ->exists();

        if ($existingTrial) {
            return back()->withErrors(['customer_phone' => 'এই নম্বর দিয়ে আগে ফ্রি ট্রায়াল নেওয়া হয়েছে।']);
        }

        // Create trial request
        $hotspotRequest = HotspotRequest::create([
            'router_id' => $validated['router_id'],
            'package_id' => null, // Trial doesn't need package
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'mac_address' => $validated['mac_address'] ?? null,
            'ip_address' => $validated['ip_address'] ?? $request->ip(),
            'payment_method' => 'trial',
            'amount' => 0,
            'status' => 'pending',
            'is_trial' => true,
            'trial_days' => 5,
            'trial_speed' => '10M/10M',
        ]);

        // Send notification to admins
        Notification::sendToAdmins(
            'trial_request',
            'নতুন ফ্রি ট্রায়াল রিকোয়েস্ট',
            "{$validated['customer_name']} ({$validated['customer_phone']}) - ৫ দিনের ফ্রি ট্রায়াল চেয়েছে।",
            [
                'request_id' => $hotspotRequest->id,
                'is_trial' => true,
            ]
        );

        return view('captive.trial-pending', [
            'request' => $hotspotRequest,
            'template' => CaptivePortalTemplate::getForRouter($validated['router_id']),
        ]);
    }

    /**
     * Show payment page
     */
    public function showPayment(Request $request)
    {
        $type = $request->query('type', 'preset');
        $mac = $request->query('mac', '');
        
        // Get first router
        $router = Router::where('is_active', true)->first();
        $template = $router ? CaptivePortalTemplate::getForRouter($router->id) : null;

        if ($type === 'custom') {
            // Custom package
            $speed = $request->query('speed', 10);
            $days = $request->query('days', 15);
            $devices = $request->query('devices', 1);
            $price = $request->query('price', 0);

            return view('captive.payment', [
                'router' => $router,
                'template' => $template,
                'clientMac' => $mac,
                'clientIp' => $request->ip(),
                'isCustom' => true,
                'customData' => [
                    'speed' => $speed,
                    'days' => $days,
                    'devices' => $devices,
                    'price' => $price,
                ],
                'package' => null,
            ]);
        } else {
            // Preset package
            $packageId = $request->query('package_id');
            $package = Package::find($packageId);

            if (!$package) {
                return redirect()->route('captive.index');
            }

            return view('captive.payment', [
                'router' => $package->router,
                'template' => $template,
                'clientMac' => $mac,
                'clientIp' => $request->ip(),
                'isCustom' => false,
                'customData' => null,
                'package' => $package,
            ]);
        }
    }

    /**
     * Submit custom package payment
     */
    public function submitCustomPayment(Request $request)
    {
        $validated = $request->validate([
            'router_id' => 'required|exists:routers,id',
            'speed' => 'required|integer|min:5|max:100',
            'days' => 'required|integer|min:5|max:90',
            'devices' => 'required|integer|min:1|max:4',
            'price' => 'required|numeric|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'payment_method' => 'required|in:bkash,nagad,rocket,cash,other',
            'transaction_id' => 'nullable|string|max:100',
            'mac_address' => 'nullable|string|max:50',
            'ip_address' => 'nullable|string|max:50',
        ]);

        // Create hotspot request for custom package
        $hotspotRequest = HotspotRequest::create([
            'router_id' => $validated['router_id'],
            'package_id' => null, // Custom doesn't have preset package
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? null,
            'mac_address' => $validated['mac_address'] ?? null,
            'ip_address' => $validated['ip_address'] ?? $request->ip(),
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'] ?? null,
            'amount' => $validated['price'],
            'status' => 'pending',
            'is_custom' => true,
            'custom_speed' => $validated['speed'] . 'M/' . $validated['speed'] . 'M',
            'custom_days' => $validated['days'],
            'custom_devices' => $validated['devices'],
        ]);

        // Send notification
        Notification::sendToAdmins(
            'custom_request',
            'নতুন কাস্টম প্যাকেজ রিকোয়েস্ট',
            "{$validated['customer_name']} ({$validated['customer_phone']}) - {$validated['speed']}Mbps × {$validated['days']}দিন × {$validated['devices']}টি ডিভাইস = ৳{$validated['price']}",
            [
                'request_id' => $hotspotRequest->id,
                'is_custom' => true,
                'speed' => $validated['speed'],
                'days' => $validated['days'],
                'devices' => $validated['devices'],
            ]
        );

        $template = CaptivePortalTemplate::getForRouter($validated['router_id']);
        return view('captive.success', [
            'request' => $hotspotRequest,
            'package' => null,
            'isCustom' => true,
            'template' => $template,
        ]);
    }

    private function getStatusMessage(HotspotRequest $request): string
    {
        return match($request->status) {
            'pending' => 'আপনার রিকোয়েস্ট পেন্ডিং আছে। অনুগ্রহ করে অপেক্ষা করুন...',
            'approved' => 'আপনার রিকোয়েস্ট অনুমোদিত হয়েছে!',
            'rejected' => 'আপনার রিকোয়েস্ট বাতিল হয়েছে। কারণ: ' . ($request->rejection_reason ?? 'N/A'),
            'expired' => 'আপনার রিকোয়েস্টের মেয়াদ শেষ হয়ে গেছে।',
            default => 'অজানা স্ট্যাটাস',
        };
    }
}
