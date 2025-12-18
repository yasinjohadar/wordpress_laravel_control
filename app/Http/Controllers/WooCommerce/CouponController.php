<?php

namespace App\Http\Controllers\WooCommerce;

use App\Http\Controllers\Controller;
use App\Models\WooCommerce\Coupon;
use App\Services\WooCommerce\Sync\CouponSyncService;
use App\Services\WooCommerce\WooCommerceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($search = $request->get('search')) {
            $query->where('code', 'like', "%{$search}%");
        }

        $coupons = $query->orderByDesc('woo_created_at')->paginate(15);

        return view('admin.woocommerce.coupons.index', compact('coupons'));
    }

    public function sync(Request $request)
    {
        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.');
            }

            $syncService = new CouponSyncService($client);
            $result = $syncService->syncAll();

            return redirect()->back()->with('success', "تم مزامنة {$result['synced']} كوبون بنجاح.");
        } catch (\Exception $e) {
            Log::error('Coupon sync error', ['error' => $e->getMessage()]);
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء المزامنة: ' . $e->getMessage());
        }
    }
}


