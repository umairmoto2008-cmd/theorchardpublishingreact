<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Voucher #{{ $productReturn->return_number }} - {{ $storeSettings['name'] }}</title>
    
    <!-- Google Fonts: Poppins & Urdu -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;700;800;900&family=Poppins:wght@400;500;600;700;800;900&family=Noto+Nastaliq+Urdu:wght@400;600;700;800&family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- JsBarcode for Return Barcode -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        body {
            background: #f1f5f9;
            color: #000000;
            font-size: 11px;
            line-height: 1.3;
            padding: 15px 0;
            -webkit-font-smoothing: antialiased;
        }

        /* 80mm Standard Size (Default) */
        .receipt-container {
            width: 72mm;
            max-width: 72mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 8px 6px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 4px;
            color: #000000;
        }

        /* 58mm Compact Size */
        .receipt-container.size-58mm {
            width: 48mm;
            max-width: 48mm;
            padding: 4px 3px;
            font-size: 9.5px;
        }

        .receipt-container.size-58mm .store-name { font-size: 13px; }
        .receipt-container.size-58mm .store-tagline { font-size: 8px; }
        .receipt-container.size-58mm .store-info { font-size: 8px; }
        .receipt-container.size-58mm .return-badge { font-size: 9.5px; padding: 1.5px 2px; }
        .receipt-container.size-58mm .meta-row { font-size: 8.5px; }
        .receipt-container.size-58mm .items-table { font-size: 8.5px; }
        .receipt-container.size-58mm .items-table th { font-size: 8px; }
        .receipt-container.size-58mm .item-name { font-size: 9px; }
        .receipt-container.size-58mm .item-rate { font-size: 8px; }
        .receipt-container.size-58mm .totals-row { font-size: 8.5px; }
        .receipt-container.size-58mm .grand-total-label { font-size: 9.5px; }
        .receipt-container.size-58mm .grand-total-val { font-size: 12px; }
        .receipt-container.size-58mm .footer-section { font-size: 8px; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-bold { font-weight: 700; }

        /* Store Header */
        .store-header {
            text-align: center;
            padding-bottom: 2px;
        }

        .store-logo {
            width: 42px;
            height: auto;
            margin-bottom: 2px;
            display: inline-block;
        }

        .store-name {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.3px;
            color: #000000;
            text-transform: uppercase;
            line-height: 1.15;
        }

        .store-tagline {
            font-size: 9.5px;
            font-weight: 600;
            color: #111111;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }

        .store-info {
            font-size: 9.5px;
            color: #111111;
            line-height: 1.25;
        }

        /* Dashed Separators */
        .divider {
            border-top: 1px dashed #000000;
            margin: 4px 0;
        }

        .divider-double {
            border-top: 1.5px solid #000000;
            margin: 4px 0;
        }

        /* Return Badge */
        .return-badge {
            font-size: 11px;
            font-weight: 800;
            text-align: center;
            background: #fff1f2;
            padding: 2.5px 4px;
            border-radius: 3px;
            margin: 3px 0;
            border: 1px solid #000000;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        /* Meta Rows */
        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 9.5px;
            line-height: 1.35;
            color: #000000;
        }

        .meta-row strong {
            color: #000000;
            font-weight: 700;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin: 2px 0;
        }

        .items-table th {
            text-align: left;
            border-bottom: 1px solid #000000;
            padding: 3px 0;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: #000000;
        }

        .items-table td {
            padding: 2.5px 0;
            vertical-align: top;
            border-bottom: 1px dashed #cccccc;
            color: #000000;
        }

        .item-name {
            font-weight: 700;
            font-size: 9.5px;
            line-height: 1.2;
            color: #000000;
        }

        .item-rate {
            font-size: 8.5px;
            color: #222222;
            margin-top: 0.5px;
        }

        /* Totals */
        .totals-section {
            margin: 3px 0;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            font-size: 9.5px;
            padding: 1.5px 0;
            color: #000000;
        }

        .refund-total-box {
            border: 1.5px solid #000000;
            border-radius: 3px;
            padding: 4px 6px;
            margin: 4px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafafa;
        }

        .grand-total-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #000000;
            letter-spacing: 0.3px;
        }

        .grand-total-val {
            font-size: 14px;
            font-weight: 900;
            color: #000000;
            font-family: 'Outfit', sans-serif;
        }

        /* Signatures */
        .signatures-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 14px;
            padding-top: 2px;
            font-size: 8.5px;
        }

        .sign-box {
            width: 45%;
            text-align: center;
            border-top: 1px solid #000000;
            padding-top: 3px;
            font-weight: 600;
        }

        /* Barcode */
        .barcode-section {
            text-align: center;
            margin-top: 4px;
            padding-top: 2px;
        }

        .barcode-svg {
            max-width: 100%;
            height: 24px;
            margin: 0 auto;
            display: block;
        }

        /* Footer */
        .footer-section {
            text-align: center;
            margin-top: 4px;
            padding-top: 2px;
            font-size: 8.5px;
            color: #111111;
            line-height: 1.3;
        }

        .urdu-blessing {
            font-family: 'Noto Sans Arabic', 'Noto Nastaliq Urdu', sans-serif;
            font-size: 9.5px;
            font-weight: 700;
            margin-bottom: 1px;
            direction: rtl;
        }

        /* Toolbar */
        .no-print-toolbar {
            text-align: center;
            margin-bottom: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-print {
            background: #991b1b;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }

        .btn-size {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-size.active {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
        }

        .btn-close {
            background: #64748b;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
        }

        /* Print Media Styles */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                box-sizing: border-box !important;
            }

            @page {
                size: auto;
                margin: 0mm !important;
            }

            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
            }

            .no-print-toolbar {
                display: none !important;
            }

            .receipt-container {
                width: 72mm !important;
                max-width: 72mm !important;
                padding: 1mm 1mm 2mm 1mm !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                margin: 0 auto !important;
                page-break-inside: auto !important;
                break-inside: auto !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
            }

            .receipt-container.size-58mm {
                width: 48mm !important;
                max-width: 48mm !important;
                padding: 0.5mm 0.5mm 1mm 0.5mm !important;
            }

            .items-table tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- On-screen Print Controls & Paper Size Switcher -->
    <div class="no-print-toolbar">
        <button onclick="window.print()" class="btn-print">Print Return Slip (واپسی پرنٹ کریں)</button>
        <button onclick="switchPaperSize('80mm')" id="btn80" class="btn-size active">80mm Paper</button>
        <button onclick="switchPaperSize('58mm')" id="btn58" class="btn-size">58mm Paper</button>
        <button onclick="window.close()" class="btn-close">Close (بند کریں)</button>
    </div>

    <!-- Thermal Receipt Container -->
    <div class="receipt-container" id="receiptBox">
        
        <!-- Header -->
        <div class="store-header">
            <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="store-logo" onerror="this.style.display='none'">
            <div class="store-name">{{ $storeSettings['name'] }}</div>
            <div class="store-tagline">{{ $storeSettings['tagline'] }}</div>
            <div class="store-info">{{ $storeSettings['address'] }}</div>
            <div class="store-info">WhatsApp / Call: <strong>{{ $storeSettings['phone'] }}</strong></div>
        </div>

        <div class="divider"></div>

        <!-- Return Voucher Badge -->
        <div class="return-badge">
            RETURN VOUCHER / واپسی واؤچر
        </div>

        <div>
            <div class="meta-row">
                <span>Return #: <strong>{{ $productReturn->return_number }}</strong></span>
                <span>Date: <strong>{{ $productReturn->created_at->format('d/m/Y') }}</strong></span>
            </div>
            <div class="meta-row">
                <span>Time: <strong>{{ $productReturn->created_at->format('h:i A') }}</strong></span>
                <span>Customer: <strong>{{ $productReturn->customer_name ?: 'Walk-in' }}</strong></span>
            </div>
            <div class="meta-row">
                @if($productReturn->sale)
                    <span>Original Bill: <strong>{{ $productReturn->sale->invoice_number }}</strong></span>
                @else
                    <span>Type: <strong>Direct Return</strong></span>
                @endif
                <span>Staff: <strong>{{ $productReturn->user->name ?? 'Staff' }}</strong></span>
            </div>
            @if($productReturn->reason)
                <div class="meta-row" style="margin-top: 1px;">
                    <span style="font-size: 8.5px; color: #222;">Reason: <em>{{ $productReturn->reason }}</em></span>
                </div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- Returned Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 52%;">Returned Item</th>
                    <th style="width: 18%;" class="text-center">Qty</th>
                    <th style="width: 30%;" class="text-right">Refund</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productReturn->items as $rItem)
                    <tr>
                        <td>
                            <div class="item-name">{{ $rItem->product_name }}</div>
                            <div class="item-rate">@ Rs. {{ number_format($rItem->unit_price, 0) }}</div>
                        </td>
                        <td class="text-center text-bold" style="font-size: 10px;">
                            {{ $rItem->quantity }}
                        </td>
                        <td class="text-right text-bold" style="font-size: 10.5px;">
                            Rs. {{ number_format($rItem->refund_amount, 0) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Refund Totals -->
        <div class="totals-section">
            <div class="totals-row">
                <span>Total Items Returned:</span>
                <span class="text-bold">{{ $productReturn->items->count() }} ({{ $productReturn->items->sum('quantity') }} Units)</span>
            </div>

            <!-- Net Refund Box -->
            <div class="refund-total-box">
                <span class="grand-total-label">TOTAL REFUND PAID:</span>
                <span class="grand-total-val">Rs. {{ number_format($productReturn->total_refund, 0) }}</span>
            </div>
        </div>

        <!-- Signatures for Shop Audit -->
        <div class="signatures-grid">
            <div class="sign-box">
                Customer Sign
            </div>
            <div class="sign-box">
                Authorized Sign
            </div>
        </div>

        <div class="divider"></div>

        <!-- Barcode of Return Number -->
        <div class="barcode-section">
            <svg class="barcode-svg" 
                 jsbarcode-value="{{ $productReturn->return_number }}" 
                 jsbarcode-format="CODE128" 
                 jsbarcode-width="1.1"
                 jsbarcode-height="22" 
                 jsbarcode-fontSize="9" 
                 jsbarcode-font="Poppins"
                 jsbarcode-displayValue="true"
                 jsbarcode-margin="0">
            </svg>
        </div>

        <!-- Footer Notice -->
        <div class="footer-section">
            <div class="urdu-blessing">شکریہ • مال واپس اور رقم ادا کر دی گئی ہے</div>
            <div style="font-weight: 600;">{{ $storeSettings['name'] }} - Pure by Nature</div>
            <div style="font-size: 8.5px; color: #444; margin-top: 2px;">This is a system generated return voucher for official records.</div>
        </div>

    </div>

    <script>
        function switchPaperSize(size) {
            const box = document.getElementById('receiptBox');
            const btn80 = document.getElementById('btn80');
            const btn58 = document.getElementById('btn58');
            if (size === '58mm') {
                box.classList.add('size-58mm');
                btn58.classList.add('active');
                btn80.classList.remove('active');
                localStorage.setItem('pos_receipt_paper_size', '58mm');
            } else {
                box.classList.remove('size-58mm');
                btn80.classList.add('active');
                btn58.classList.remove('active');
                localStorage.setItem('pos_receipt_paper_size', '80mm');
            }
            renderReturnBarcode();
        }

        // Restore saved paper preference
        const savedSize = localStorage.getItem('pos_receipt_paper_size');
        if (savedSize === '58mm') {
            switchPaperSize('58mm');
        }

        function renderReturnBarcode() {
            try {
                if (typeof JsBarcode === 'function') {
                    const is58 = document.getElementById('receiptBox')?.classList.contains('size-58mm');
                    JsBarcode(".barcode-svg", "{{ $productReturn->return_number }}", {
                        format: "CODE128",
                        width: is58 ? 0.9 : 1.1,
                        height: is58 ? 18 : 22,
                        fontSize: is58 ? 8 : 9,
                        font: "Poppins",
                        displayValue: true,
                        margin: 0
                    });
                }
            } catch(e) {
                console.error("JsBarcode return init error:", e);
            }
        }

        renderReturnBarcode();
        document.addEventListener('DOMContentLoaded', renderReturnBarcode);
        window.addEventListener('load', renderReturnBarcode);

        // Auto trigger print only when loaded directly in new window/tab
        if (window.self === window.top) {
            function triggerPrint() {
                setTimeout(() => {
                    renderReturnBarcode();
                    window.focus();
                    window.print();
                }, 300);
            }

            if (document.readyState === 'complete') {
                triggerPrint();
            } else {
                window.addEventListener('load', triggerPrint);
            }
        }
    </script>
</body>
</html>
