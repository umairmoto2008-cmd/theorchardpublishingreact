<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode - {{ $singleProduct ? $singleProduct->name : 'Hashmi Grains' }}</title>
    <!-- Google Fonts: Poppins & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        body { background: #f1f5f9; padding: 15px; color: #1e293b; }
        
        .no-print-bar {
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .product-badge-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .prod-avatar {
            width: 44px;
            height: 44px;
            background: #ecfdf5;
            color: #0f5132;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            border: 1px solid #a7f3d0;
        }

        .controls-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .mode-btn {
            padding: 7px 14px;
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            font-size: 0.82rem;
            font-weight: 700;
            color: #475569;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }

        .mode-btn.active {
            background: #0f5132;
            color: #ffffff;
            border-color: #0f5132;
        }

        .copies-stepper {
            display: inline-flex;
            align-items: center;
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
        }

        .copies-btn {
            background: #ffffff;
            border: none;
            width: 32px;
            height: 36px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            color: #334155;
            transition: background 0.15s;
        }

        .copies-btn:hover {
            background: #e2e8f0;
        }

        .copies-input {
            width: 50px;
            height: 36px;
            border: none;
            border-left: 1px solid #cbd5e1;
            border-right: 1px solid #cbd5e1;
            text-align: center;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 0.95rem;
            background: #ffffff;
        }

        .preset-btn {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            font-size: 0.78rem;
            font-weight: 700;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .preset-btn:hover {
            background: #ecfdf5;
            color: #0f5132;
            border-color: #a7f3d0;
        }

        .btn-print {
            background: #16a34a;
            color: white;
            padding: 9px 20px;
            border-radius: 8px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            font-size: 0.92rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(22, 163, 74, 0.3);
            transition: all 0.15s ease;
        }

        .btn-print:hover {
            background: #15803d;
            transform: translateY(-1px);
        }

        .btn-close-window {
            padding: 9px 16px;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.88rem;
        }

        .print-tip {
            background: #fefce8;
            border: 1px solid #fef08a;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.8rem;
            color: #854d0e;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Labels Container: Default Thermal Roll (50mm x 30mm) */
        .labels-roll-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin: 0 auto;
        }

        .labels-grid-wrap {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 14px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* 50x30mm Standard Thermal Sticker Box */
        .label-sticker {
            width: 50mm;
            height: 30mm;
            max-height: 30mm;
            background: white;
            border: 1px dashed #000000;
            padding: 1.2mm 1.5mm;
            text-align: center;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border-radius: 3px;
        }

        .label-store {
            font-size: 6.8pt;
            font-weight: 800;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            line-height: 1;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
        }

        .label-name {
            font-size: 8.5pt;
            font-weight: 800;
            color: #000000;
            line-height: 1.1;
            text-transform: uppercase;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            margin: 0.2mm 0;
        }

        .barcode-svg {
            max-width: 44mm !important;
            height: 9.5mm !important;
            display: block;
            margin: 0 auto;
            image-rendering: pixelated;
        }

        .label-price {
            font-family: 'Outfit', sans-serif;
            font-size: 11pt;
            font-weight: 900;
            color: #000000;
            line-height: 1;
            margin-top: 0.2mm;
        }

        .label-unit {
            font-size: 7pt;
            color: #222222;
            font-weight: 600;
        }

        .label-custom {
            font-size: 6.5pt;
            font-weight: 800;
            color: #000000;
            line-height: 1;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
        }

        /* PRINT STYLES FOR THERMAL LABEL PRINTER */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                box-sizing: border-box !important;
            }

            @page {
                size: 50mm 30mm;
                margin: 0mm !important;
            }

            html, body { 
                background: #ffffff !important; 
                padding: 0 !important; 
                margin: 0 !important; 
                width: 100% !important;
            }

            .no-print-bar, .print-tip { 
                display: none !important; 
            }

            .labels-roll-wrap, .labels-grid-wrap {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .label-sticker {
                width: 50mm !important;
                height: 30mm !important;
                max-height: 30mm !important;
                border: none !important;
                box-shadow: none !important;
                page-break-after: always !important;
                break-after: page !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin: 0 !important;
                padding: 1.2mm 1.5mm !important;
                background: #ffffff !important;
                display: flex !important;
            }

            /* CRUCIAL: Do not break after last sticker to avoid feeding empty label */
            .label-sticker:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }

            .label-store, .label-name, .label-price, .label-custom {
                color: #000000 !important;
            }
        }
    </style>
</head>
<body>
    <!-- Top Action & Quantity Controls (Not printed) -->
    <div class="no-print-bar">
        <div class="product-badge-info">
            <div class="prod-avatar">
                <i class="fa-solid fa-barcode"></i>
            </div>
            <div>
                @if($singleProduct)
                    <h2 style="color: #0f172a; font-size: 1.15rem; font-weight: 800; text-transform: uppercase;">
                        {{ $singleProduct->name }}
                    </h2>
                    <p style="font-size: 0.8rem; color: #64748b;">
                        Barcode: <code style="font-weight: 700; color: #0f5132;">{{ $singleProduct->barcode }}</code> &bull; 
                        Price: <strong style="color: #0f172a;">Rs {{ number_format($singleProduct->sale_price, 0) }}</strong>
                    </p>
                @else
                    <h2 style="color: #0f5132; font-size: 1.15rem; font-weight: 800;">
                        Product Barcode Labels Sheet
                    </h2>
                    <p style="font-size: 0.8rem; color: #64748b;">
                        Total {{ $products->count() }} active products ready to print.
                    </p>
                @endif
            </div>
        </div>

        <div class="controls-group">
            <!-- Mode Toggle -->
            <button type="button" class="mode-btn active" id="btnRollMode" onclick="setPrintMode('roll')">
                <i class="fa-solid fa-scroll"></i> Label Roll (50×30mm)
            </button>
            <button type="button" class="mode-btn" id="btnSheetMode" onclick="setPrintMode('sheet')">
                <i class="fa-solid fa-file"></i> A4 Sheet (4 Cols)
            </button>

            @if($singleProduct)
                <!-- Single Product Copies Stepper -->
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #475569;">Copies:</span>
                    <div class="copies-stepper">
                        <button type="button" class="copies-btn" onclick="updateCopies(-1)">-</button>
                        <input type="number" id="copiesInput" class="copies-input" value="{{ $copies }}" min="1" max="500" onchange="renderDynamicStickers()">
                        <button type="button" class="copies-btn" onclick="updateCopies(1)">+</button>
                    </div>
                </div>

                <!-- Quick Presets -->
                <div style="display: flex; gap: 4px;">
                    <button type="button" class="preset-btn" onclick="setPreset(1)">1x</button>
                    <button type="button" class="preset-btn" onclick="setPreset(5)">5x</button>
                    <button type="button" class="preset-btn" onclick="setPreset(10)">10x</button>
                    <button type="button" class="preset-btn" onclick="setPreset(20)">20x</button>
                </div>
            @endif

            <button onclick="window.print()" class="btn-print">
                <i class="fa-solid fa-print"></i> Print Labels
            </button>
            <button onclick="window.close()" class="btn-close-window">Close</button>
        </div>
    </div>

    <!-- Helpful Printing Tip for Label Printer Setup -->
    <div class="print-tip">
        <i class="fa-solid fa-circle-info"></i>
        <span><strong>Label Printer Tip:</strong> In the Chrome Print Dialog, select your <strong>Label Printer</strong>, set <strong>Margins = None</strong>, and uncheck <strong>Headers and Footers</strong>.</span>
    </div>

    <!-- Labels Container -->
    <div class="labels-roll-wrap" id="labelsGridContainer">
        @if($singleProduct)
            @for($i = 0; $i < $copies; $i++)
                <div class="label-sticker">
                    <div class="label-store">{{ $singleProduct->category ? $singleProduct->category->name : 'HASHMI GRAINS' }}</div>
                    <div class="label-name">{{ $singleProduct->name }}</div>
                    <svg class="barcode-svg" 
                         jsbarcode-value="{{ $singleProduct->barcode }}" 
                         jsbarcode-format="CODE128" 
                         jsbarcode-height="24" 
                         jsbarcode-width="1.1" 
                         jsbarcode-fontSize="8" 
                         jsbarcode-font="Outfit, monospace"
                         jsbarcode-displayValue="true"
                         jsbarcode-margin="0"></svg>
                    <div class="label-price">
                        Rs {{ number_format($singleProduct->sale_price, 0) }} 
                        <span class="label-unit">/ {{ $singleProduct->unit }}</span>
                    </div>
                    @if(!empty($customText))
                        <div class="label-custom">{{ $customText }}</div>
                    @elseif($singleProduct->expiry_date)
                        <div class="label-custom">Exp: {{ $singleProduct->expiry_date->format('d/m/Y') }}</div>
                    @endif
                </div>
            @endfor
        @else
            @foreach($products as $p)
                <div class="label-sticker">
                    <div class="label-store">{{ $p->category ? $p->category->name : 'HASHMI GRAINS' }}</div>
                    <div class="label-name">{{ $p->name }}</div>
                    <svg class="barcode-svg" 
                         jsbarcode-value="{{ $p->barcode }}" 
                         jsbarcode-format="CODE128" 
                         jsbarcode-height="24" 
                         jsbarcode-width="1.1" 
                         jsbarcode-fontSize="8" 
                         jsbarcode-font="Outfit, monospace"
                         jsbarcode-displayValue="true"
                         jsbarcode-margin="0"></svg>
                    <div class="label-price">
                        Rs {{ number_format($p->sale_price, 0) }} 
                        <span class="label-unit">/ {{ $p->unit }}</span>
                    </div>
                    @if($p->expiry_date)
                        <div class="label-custom">Exp: {{ $p->expiry_date->format('d/m/Y') }}</div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    <script>
        let currentMode = 'roll';

        function setPrintMode(mode) {
            currentMode = mode;
            const container = document.getElementById('labelsGridContainer');
            const btnRoll = document.getElementById('btnRollMode');
            const btnSheet = document.getElementById('btnSheetMode');

            if (mode === 'sheet') {
                container.className = 'labels-grid-wrap';
                btnSheet.classList.add('active');
                btnRoll.classList.remove('active');
            } else {
                container.className = 'labels-roll-wrap';
                btnRoll.classList.add('active');
                btnSheet.classList.remove('active');
            }
        }

        @if($singleProduct)
        const singleProdData = {
            header: @js($singleProduct->category ? $singleProduct->category->name : 'HASHMI GRAINS'),
            name: @js($singleProduct->name),
            barcode: @js($singleProduct->barcode),
            price: @js(number_format($singleProduct->sale_price, 0)),
            unit: @js($singleProduct->unit),
            custom: @js($customText ?: ($singleProduct->expiry_date ? 'Exp: ' . $singleProduct->expiry_date->format('d/m/Y') : ''))
        };

        function updateCopies(delta) {
            const input = document.getElementById('copiesInput');
            let val = parseInt(input.value) || 1;
            val = Math.max(1, Math.min(500, val + delta));
            input.value = val;
            renderDynamicStickers();
        }

        function setPreset(count) {
            const input = document.getElementById('copiesInput');
            input.value = count;
            renderDynamicStickers();
        }

        function renderDynamicStickers() {
            const input = document.getElementById('copiesInput');
            const count = Math.max(1, Math.min(500, parseInt(input.value) || 1));
            const container = document.getElementById('labelsGridContainer');
            
            let html = '';
            for (let i = 0; i < count; i++) {
                html += `
                <div class="label-sticker">
                    <div class="label-store">${singleProdData.header}</div>
                    <div class="label-name">${singleProdData.name}</div>
                    <svg class="barcode-svg" 
                         jsbarcode-value="${singleProdData.barcode}" 
                         jsbarcode-format="CODE128" 
                         jsbarcode-height="24" 
                         jsbarcode-width="1.1" 
                         jsbarcode-fontSize="8" 
                         jsbarcode-font="Outfit, monospace"
                         jsbarcode-displayValue="true"
                         jsbarcode-margin="0"></svg>
                    <div class="label-price">
                        Rs ${singleProdData.price} 
                        <span class="label-unit">/ ${singleProdData.unit}</span>
                    </div>
                    ${singleProdData.custom ? `<div class="label-custom">${singleProdData.custom}</div>` : ''}
                </div>`;
            }
            container.innerHTML = html;
            initBarcodes();
        }
        @endif

        function initBarcodes() {
            try {
                if (typeof JsBarcode === 'function') {
                    JsBarcode(".barcode-svg").init();
                }
            } catch(e) {
                console.error("Barcode init error:", e);
            }
        }
        document.addEventListener('DOMContentLoaded', initBarcodes);
        window.addEventListener('load', initBarcodes);

        @if($autoPrint)
        window.addEventListener('load', function() {
            setTimeout(function() {
                initBarcodes();
                window.focus();
                window.print();
            }, 350);
        });
        @endif
    </script>
</body>
</html>
