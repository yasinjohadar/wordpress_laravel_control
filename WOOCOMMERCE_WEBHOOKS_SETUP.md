# إعداد المزامنة التلقائية مع WooCommerce

## الطريقة 1: Webhooks (مزامنة فورية)

### خطوات الإعداد:

1. **في WooCommerce Admin:**
   - اذهب إلى: `WooCommerce > Settings > Advanced > Webhooks`
   - اضغط "Add webhook"

2. **إعدادات Webhook:**
   - **Name**: Laravel Dashboard Sync
   - **Status**: Active
   - **Topic**: اختر أحد الخيارات:
     - `Product created` → `https://your-laravel-app.com/woocommerce/webhook`
     - `Product updated` → `https://your-laravel-app.com/woocommerce/webhook`
     - `Product deleted` → `https://your-laravel-app.com/woocommerce/webhook`
     - `Order created` → `https://your-laravel-app.com/woocommerce/webhook`
     - `Order updated` → `https://your-laravel-app.com/woocommerce/webhook`
     - `Order deleted` → `https://your-laravel-app.com/woocommerce/webhook`
     - `Customer created` → `https://your-laravel-app.com/woocommerce/webhook`
     - `Customer updated` → `https://your-laravel-app.com/woocommerce/webhook`
     - `Customer deleted` → `https://your-laravel-app.com/woocommerce/webhook`
     - `Coupon created` → `https://your-laravel-app.com/woocommerce/webhook`
     - `Coupon updated` → `https://your-laravel-app.com/woocommerce/webhook`
     - `Coupon deleted` → `https://your-laravel-app.com/woocommerce/webhook`
   - **Secret**: أضف secret key (اختياري للأمان)
   - **API Version**: WC API v3

3. **في ملف `.env`:**
   ```env
   WOOCOMMERCE_WEBHOOK_SECRET=your_secret_key_here
   ```

4. **ملاحظة:** يجب إنشاء webhook منفصل لكل حدث (created, updated, deleted) ولكل نوع (product, order, customer, coupon)

## الطريقة 2: Scheduled Tasks (مزامنة دورية)

### إعداد Cron Job:

أضف السطر التالي في crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### المزامنات المحددة:

- **كل 15 دقيقة**: مزامنة شاملة (جميع البيانات)
- **كل 5 دقائق**: مزامنة الطلبات فقط (لأنها تتغير كثيراً)

### تشغيل المزامنة يدوياً:

```bash
# مزامنة كل شيء
php artisan woocommerce:sync

# مزامنة نوع محدد
php artisan woocommerce:sync --type=products
php artisan woocommerce:sync --type=orders
php artisan woocommerce:sync --type=customers
php artisan woocommerce:sync --type=coupons
```

## المزايا:

### Webhooks:
- ✅ مزامنة فورية عند حدوث التغييرات
- ✅ لا يستهلك موارد إلا عند الحاجة
- ✅ دقيق 100%

### Scheduled Tasks:
- ✅ يعمل حتى لو فشلت Webhooks
- ✅ يضمن عدم فقدان البيانات
- ✅ مزامنة دورية منتظمة

## التوصية:

استخدم **كلا الطريقتين** معاً للحصول على أفضل النتائج:
- Webhooks للمزامنة الفورية
- Scheduled Tasks كنسخة احتياطية

