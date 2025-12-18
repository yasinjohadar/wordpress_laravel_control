# دليل روابط النظام - WooCommerce Integration

## 📍 روابط الصفحات الرئيسية

### لوحة التحكم
- **لوحة المتجر**: `/woocommerce` أو `route('woocommerce.dashboard')`
- **لوحة التقارير**: `/woocommerce/reports` أو `route('woocommerce.reports.index')`

---

## 🛍️ إدارة المنتجات

### الصفحات
- **قائمة المنتجات**: `/woocommerce/products` أو `route('woocommerce.products.index')`
- **إنشاء منتج جديد**: `/woocommerce/products/create` أو `route('woocommerce.products.create')`
- **عرض منتج**: `/woocommerce/products/{id}` أو `route('woocommerce.products.show', $id)`
- **تعديل منتج**: `/woocommerce/products/{id}/edit` أو `route('woocommerce.products.edit', $id)`

### العمليات
- **مزامنة المنتجات**: `POST /woocommerce/products/sync` أو `route('woocommerce.products.sync')`
- **حفظ منتج جديد**: `POST /woocommerce/products` أو `route('woocommerce.products.store')`
- **تحديث منتج**: `PUT /woocommerce/products/{id}` أو `route('woocommerce.products.update', $id)`
- **حذف منتج**: `DELETE /woocommerce/products/{id}` أو `route('woocommerce.products.destroy', $id)`

---

## 📦 إدارة الطلبات

### الصفحات
- **قائمة الطلبات**: `/woocommerce/orders` أو `route('woocommerce.orders.index')`
- **عرض طلب**: `/woocommerce/orders/{id}` أو `route('woocommerce.orders.show', $id)`
- **طباعة الفاتورة**: `/woocommerce/orders/{id}/invoice` أو `route('woocommerce.orders.invoice', $id)`

### العمليات
- **مزامنة الطلبات**: `POST /woocommerce/orders/sync` أو `route('woocommerce.orders.sync')`
- **تحديث حالة الطلب**: `PUT /woocommerce/orders/{id}/status` أو `route('woocommerce.orders.update-status', $id)`
- **إضافة ملاحظة**: `POST /woocommerce/orders/{id}/notes` أو `route('woocommerce.orders.add-note', $id)`
- **تصدير CSV**: `/woocommerce/orders/export` أو `route('woocommerce.orders.export')`

---

## 👥 إدارة العملاء

### الصفحات
- **قائمة العملاء**: `/woocommerce/customers` أو `route('woocommerce.customers.index')`
- **إنشاء عميل جديد**: `/woocommerce/customers/create` أو `route('woocommerce.customers.create')`
- **عرض عميل**: `/woocommerce/customers/{id}` أو `route('woocommerce.customers.show', $id)`
- **تعديل عميل**: `/woocommerce/customers/{id}/edit` أو `route('woocommerce.customers.edit', $id)`

### العمليات
- **مزامنة العملاء**: `POST /woocommerce/customers/sync` أو `route('woocommerce.customers.sync')`
- **حفظ عميل جديد**: `POST /woocommerce/customers` أو `route('woocommerce.customers.store')`
- **تحديث عميل**: `PUT /woocommerce/customers/{id}` أو `route('woocommerce.customers.update', $id)`
- **حذف عميل**: `DELETE /woocommerce/customers/{id}` أو `route('woocommerce.customers.destroy', $id)`

---

## 🎟️ إدارة الكوبونات

### الصفحات
- **قائمة الكوبونات**: `/woocommerce/coupons` أو `route('woocommerce.coupons.index')`

### العمليات
- **مزامنة الكوبونات**: `POST /woocommerce/coupons/sync` أو `route('woocommerce.coupons.sync')`

---

## 📊 التقارير والإحصائيات

### الصفحات
- **التقارير الرئيسية**: `/woocommerce/reports` أو `route('woocommerce.reports.index')`
- **تقرير المبيعات**: `/woocommerce/reports/sales` أو `route('woocommerce.reports.sales')`
- **تقرير المنتجات**: `/woocommerce/reports/products` أو `route('woocommerce.reports.products')`
- **تقرير العملاء**: `/woocommerce/reports/customers` أو `route('woocommerce.reports.customers')`

### API
- **بيانات الرسم البياني**: `/woocommerce/reports/chart-data` أو `route('woocommerce.reports.chart-data')`

---

## ⚙️ العمليات العامة

### المزامنة
- **مزامنة الكل**: `POST /woocommerce/sync` أو `route('woocommerce.sync')`
- **اختبار الاتصال**: `GET /woocommerce/test-connection` أو `route('woocommerce.test-connection')`

### Webhooks
- **استقبال Webhooks**: `POST /woocommerce/webhook` أو `route('woocommerce.webhook')`

---

## 🔗 الروابط في Sidebar

جميع الروابط متاحة في Sidebar تحت قسم "إدارة المتجر":

1. **لوحة المتجر** - `/woocommerce`
2. **المنتجات** - `/woocommerce/products`
3. **الطلبات** - `/woocommerce/orders`
4. **العملاء** - `/woocommerce/customers`
5. **الكوبونات** - `/woocommerce/coupons`
6. **التقارير والإحصائيات** - `/woocommerce/reports`

---

## 📝 ملاحظات

- جميع المسارات محمية بـ `auth` و `check.user.active` middleware
- مسار Webhook غير محمي (لأنه يستقبل طلبات من WooCommerce)
- يمكن استخدام `route()` helper في Blade templates
- يمكن استخدام `url()` helper للحصول على URL كامل

---

## 🎯 أمثلة الاستخدام في Blade

```blade
{{-- رابط بسيط --}}
<a href="{{ route('woocommerce.products.index') }}">المنتجات</a>

{{-- رابط مع معاملات --}}
<a href="{{ route('woocommerce.products.show', $product->id) }}">عرض المنتج</a>

{{-- رابط مع query parameters --}}
<a href="{{ route('woocommerce.orders.index', ['status' => 'completed']) }}">الطلبات المكتملة</a>

{{-- رابط تصدير --}}
<a href="{{ route('woocommerce.orders.export', request()->all()) }}">تصدير</a>
```

---

## 🔍 البحث والفلترة

### الطلبات
- البحث: `?search=رقم_الطلب`
- الحالة: `?status=completed`
- التاريخ من: `?date_from=2024-01-01`
- التاريخ إلى: `?date_to=2024-12-31`
- الحد الأدنى: `?min_amount=100`
- الحد الأقصى: `?max_amount=1000`

### المنتجات
- البحث: `?search=اسم_المنتج`
- الحالة: `?status=publish`

### العملاء
- البحث: `?search=اسم_أو_بريد`

---

تم إنشاء هذا الدليل في: {{ date('Y-m-d H:i:s') }}

