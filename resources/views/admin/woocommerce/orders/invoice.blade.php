<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة #{{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .invoice-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .invoice-header p {
            color: #666;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-box {
            flex: 1;
        }
        .info-box h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }
        .info-box p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th,
        table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            margin-top: 20px;
            margin-left: auto;
            width: 300px;
        }
        .totals table {
            margin-bottom: 0;
        }
        .totals .total-row {
            font-size: 18px;
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        @media print {
            body {
                padding: 0;
            }
            .invoice-container {
                border: none;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h1>فاتورة</h1>
            <p>رقم الطلب: #{{ $order->order_number }}</p>
            <p>تاريخ: {{ $order->woo_created_at?->format('Y-m-d H:i:s') }}</p>
        </div>

        <div class="invoice-info">
            <div class="info-box">
                <h3>معلومات العميل</h3>
                @if($order->customer)
                    <p><strong>الاسم:</strong> {{ $order->customer->full_name }}</p>
                    <p><strong>البريد:</strong> {{ $order->customer->email }}</p>
                @endif
                @php
                    $billing = is_array($order->billing_address) ? $order->billing_address : [];
                @endphp
                @if(!empty($billing))
                    <p><strong>العنوان:</strong></p>
                    <p>
                        {{ $billing['address_1'] ?? '' }}<br>
                        {{ $billing['city'] ?? '' }}, {{ $billing['state'] ?? '' }}<br>
                        {{ $billing['postcode'] ?? '' }}, {{ $billing['country'] ?? '' }}
                    </p>
                    @if(isset($billing['phone']))
                        <p><strong>الهاتف:</strong> {{ $billing['phone'] }}</p>
                    @endif
                @endif
            </div>
            <div class="info-box">
                <h3>معلومات الطلب</h3>
                <p><strong>الحالة:</strong> 
                    {{ match($order->status) {
                        'pending' => 'قيد الانتظار',
                        'processing' => 'قيد المعالجة',
                        'on-hold' => 'معلق',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغي',
                        'refunded' => 'مسترد',
                        'failed' => 'فاشل',
                        default => $order->status
                    } }}
                </p>
                <p><strong>طريقة الدفع:</strong> {{ $order->payment_method_title ?? 'N/A' }}</p>
                @if($order->transaction_id)
                    <p><strong>رقم المعاملة:</strong> {{ $order->transaction_id }}</p>
                @endif
                @if($order->date_paid)
                    <p><strong>تاريخ الدفع:</strong> {{ $order->date_paid->format('Y-m-d H:i:s') }}</p>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th class="text-center">الكمية</th>
                    <th class="text-right">السعر</th>
                    <th class="text-right">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $lineItems = is_array($order->line_items) ? $order->line_items : [];
                @endphp
                @forelse($lineItems as $item)
                    <tr>
                        <td>
                            <strong>{{ $item['name'] ?? 'N/A' }}</strong>
                            @if(isset($item['sku']))
                                <br><small>SKU: {{ $item['sku'] }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item['quantity'] ?? 0 }}</td>
                        <td class="text-right">{{ number_format($item['price'] ?? 0, 2) }} {{ $order->currency_symbol ?? 'ر.س' }}</td>
                        <td class="text-right">{{ number_format(($item['quantity'] ?? 0) * ($item['price'] ?? 0), 2) }} {{ $order->currency_symbol ?? 'ر.س' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">لا توجد عناصر</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td class="text-right">المجموع الفرعي:</td>
                    <td class="text-right">{{ number_format($order->subtotal, 2) }} {{ $order->currency_symbol ?? 'ر.س' }}</td>
                </tr>
                @if($order->discount_total > 0)
                    <tr>
                        <td class="text-right">الخصم:</td>
                        <td class="text-right">-{{ number_format($order->discount_total, 2) }} {{ $order->currency_symbol ?? 'ر.س' }}</td>
                    </tr>
                @endif
                @if($order->shipping_total > 0)
                    <tr>
                        <td class="text-right">الشحن:</td>
                        <td class="text-right">{{ number_format($order->shipping_total, 2) }} {{ $order->currency_symbol ?? 'ر.س' }}</td>
                    </tr>
                @endif
                @if($order->total_tax > 0)
                    <tr>
                        <td class="text-right">الضريبة:</td>
                        <td class="text-right">{{ number_format($order->total_tax, 2) }} {{ $order->currency_symbol ?? 'ر.س' }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td class="text-right">الإجمالي:</td>
                    <td class="text-right">{{ $order->formatted_total }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>شكراً لكم على شرائكم!</p>
            <p>هذه الفاتورة تم إنشاؤها تلقائياً من نظام إدارة المتجر</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" class="btn btn-primary">طباعة</button>
        <button onclick="window.close()" class="btn btn-secondary">إغلاق</button>
    </div>
</body>
</html>

