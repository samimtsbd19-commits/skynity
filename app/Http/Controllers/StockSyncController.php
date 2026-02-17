<?php

namespace App\Http\Controllers;

use App\Models\StockUser;
use Illuminate\Http\Request;

class StockSyncController extends Controller
{
    public function pending(Request $request)
    {
        $routerId = $request->query('router_id');
        $query = StockUser::where('status', 'assigned')
            ->whereNull('enabled_on_router_at');
        if ($routerId) {
            $query->where('router_id', $routerId);
        }
        $items = $query->take(100)->get()->map(function ($su) {
            $req = $su->hotspotRequest;
            return [
                'id' => $su->id,
                'router_id' => $su->router_id,
                'username' => $su->username,
                'password' => $su->password,
                'mac' => $req?->mac_address,
            ];
        });
        return response()->json(['data' => $items]);
    }

    public function markEnabled(Request $request)
    {
        $id = $request->input('id');
        $su = StockUser::findOrFail($id);
        $su->update(['enabled_on_router_at' => now()]);
        return response()->json(['ok' => true]);
    }
}
