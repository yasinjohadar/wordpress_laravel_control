<?php

namespace App\Http\Controllers\WooCommerce;

use App\Http\Controllers\Controller;
use App\Models\WooCommerce\Customer;
use App\Models\WooCommerce\Order;
use App\Services\WooCommerce\CustomerService;
use App\Services\WooCommerce\Sync\CustomerSyncService;
use App\Services\WooCommerce\WooCommerceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderByDesc('woo_created_at')->paginate(15);

        return view('admin.woocommerce.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.woocommerce.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'billing.first_name' => 'nullable|string|max:255',
            'billing.last_name' => 'nullable|string|max:255',
            'billing.company' => 'nullable|string|max:255',
            'billing.address_1' => 'nullable|string|max:255',
            'billing.address_2' => 'nullable|string|max:255',
            'billing.city' => 'nullable|string|max:255',
            'billing.state' => 'nullable|string|max:255',
            'billing.postcode' => 'nullable|string|max:20',
            'billing.country' => 'nullable|string|max:2',
            'billing.email' => 'nullable|email|max:255',
            'billing.phone' => 'nullable|string|max:20',
            'shipping.first_name' => 'nullable|string|max:255',
            'shipping.last_name' => 'nullable|string|max:255',
            'shipping.company' => 'nullable|string|max:255',
            'shipping.address_1' => 'nullable|string|max:255',
            'shipping.address_2' => 'nullable|string|max:255',
            'shipping.city' => 'nullable|string|max:255',
            'shipping.state' => 'nullable|string|max:255',
            'shipping.postcode' => 'nullable|string|max:20',
            'shipping.country' => 'nullable|string|max:2',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'first_name.required' => 'الاسم الأول مطلوب',
            'last_name.required' => 'الاسم الأخير مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
        ]);

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.')->withInput();
            }

            $customerService = new CustomerService($client);
            
            // إنشاء العميل في WooCommerce
            $wooCustomer = $customerService->create($validated);

            // مزامنة العميل الجديد إلى قاعدة البيانات المحلية
            $syncService = new CustomerSyncService($client);
            $syncService->syncCustomer($wooCustomer);

            return redirect()->route('woocommerce.customers.index')
                ->with('success', 'تم إنشاء العميل بنجاح في WooCommerce.');
        } catch (\Exception $e) {
            Log::error('Customer creation error', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء العميل: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        
        // جلب طلبات العميل
        $orders = Order::where('customer_id', $customer->woo_id)
            ->orderByDesc('woo_created_at')
            ->paginate(10);
        
        // محاولة جلب أحدث بيانات من WooCommerce إذا كان متاحاً
        $freshData = null;
        try {
            $client = new WooCommerceClient();
            if ($client->isConfigured() && $customer->woo_id) {
                $freshData = $client->get("customers/{$customer->woo_id}");
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch fresh customer data', [
                'customer_id' => $id,
                'woo_id' => $customer->woo_id,
                'error' => $e->getMessage()
            ]);
        }

        return view('admin.woocommerce.customers.show', compact('customer', 'orders', 'freshData'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        
        if (!$customer->woo_id) {
            return redirect()->route('woocommerce.customers.index')
                ->with('error', 'لا يمكن تعديل هذا العميل لأنه غير مرتبط بـ WooCommerce.');
        }

        return view('admin.woocommerce.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        if (!$customer->woo_id) {
            return redirect()->route('woocommerce.customers.index')
                ->with('error', 'لا يمكن تعديل هذا العميل لأنه غير مرتبط بـ WooCommerce.');
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',
            'billing.first_name' => 'nullable|string|max:255',
            'billing.last_name' => 'nullable|string|max:255',
            'billing.company' => 'nullable|string|max:255',
            'billing.address_1' => 'nullable|string|max:255',
            'billing.address_2' => 'nullable|string|max:255',
            'billing.city' => 'nullable|string|max:255',
            'billing.state' => 'nullable|string|max:255',
            'billing.postcode' => 'nullable|string|max:20',
            'billing.country' => 'nullable|string|max:2',
            'billing.email' => 'nullable|email|max:255',
            'billing.phone' => 'nullable|string|max:20',
            'shipping.first_name' => 'nullable|string|max:255',
            'shipping.last_name' => 'nullable|string|max:255',
            'shipping.company' => 'nullable|string|max:255',
            'shipping.address_1' => 'nullable|string|max:255',
            'shipping.address_2' => 'nullable|string|max:255',
            'shipping.city' => 'nullable|string|max:255',
            'shipping.state' => 'nullable|string|max:255',
            'shipping.postcode' => 'nullable|string|max:20',
            'shipping.country' => 'nullable|string|max:2',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'first_name.required' => 'الاسم الأول مطلوب',
            'last_name.required' => 'الاسم الأخير مطلوب',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
        ]);

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.')->withInput();
            }

            $customerService = new CustomerService($client);
            
            // تحديث العميل في WooCommerce
            $wooCustomer = $customerService->update($customer->woo_id, $validated);

            // مزامنة العميل المحدث إلى قاعدة البيانات المحلية
            $syncService = new CustomerSyncService($client);
            $syncService->syncCustomer($wooCustomer);

            return redirect()->route('woocommerce.customers.show', $customer->id)
                ->with('success', 'تم تحديث العميل بنجاح في WooCommerce.');
        } catch (\Exception $e) {
            Log::error('Customer update error', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث العميل: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        
        if (!$customer->woo_id) {
            return redirect()->route('woocommerce.customers.index')
                ->with('error', 'لا يمكن حذف هذا العميل لأنه غير مرتبط بـ WooCommerce.');
        }

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.');
            }

            $customerService = new CustomerService($client);
            
            // حذف العميل من WooCommerce
            $customerService->delete($customer->woo_id, true);

            // حذف العميل من قاعدة البيانات المحلية
            $customer->delete();

            return redirect()->route('woocommerce.customers.index')
                ->with('success', 'تم حذف العميل بنجاح من WooCommerce.');
        } catch (\Exception $e) {
            Log::error('Customer deletion error', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف العميل: ' . $e->getMessage());
        }
    }

    public function sync(Request $request)
    {
        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.');
            }

            $syncService = new CustomerSyncService($client);
            $result = $syncService->syncAll();

            return redirect()->back()->with('success', "تم مزامنة {$result['synced']} عميل بنجاح.");
        } catch (\Exception $e) {
            Log::error('Customer sync error', ['error' => $e->getMessage()]);
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء المزامنة: ' . $e->getMessage());
        }
    }
}


