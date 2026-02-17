<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ভাউচার প্রিন্ট - SKYNITY WiFi</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Noto Sans Bengali', Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .print-controls {
            background: #4f46e5;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .print-btn {
            background: white;
            color: #4f46e5;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }
        
        .print-btn:hover {
            background: #f0f0f0;
        }
        
        .vouchers-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .voucher-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            page-break-inside: avoid;
        }
        
        .voucher-logo {
            font-size: 20px;
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 10px;
        }
        
        .voucher-package {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .voucher-credentials {
            background: #f8fafc;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .credential-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .credential-row:last-child {
            margin-bottom: 0;
        }
        
        .credential-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
        }
        
        .credential-value {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            letter-spacing: 1px;
        }
        
        .voucher-info {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        
        .voucher-qr {
            margin: 10px 0;
        }
        
        .voucher-qr canvas {
            width: 80px !important;
            height: 80px !important;
        }
        
        .voucher-footer {
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px dashed #e5e7eb;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .print-controls {
                display: none !important;
            }
            
            .vouchers-grid {
                gap: 10px;
            }
            
            .voucher-card {
                border: 1px solid #ccc;
                padding: 15px;
            }
        }
        
        @page {
            margin: 10mm;
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <div>
            <strong>SKYNITY WiFi</strong> - {{ count($vouchers) }} টি ভাউচার
        </div>
        <button class="print-btn" onclick="window.print()">
            🖨️ প্রিন্ট করুন
        </button>
    </div>
    
    <div class="vouchers-grid">
        @foreach($vouchers as $voucher)
        <div class="voucher-card">
            <div class="voucher-logo">📶 SKYNITY WiFi</div>
            
            @if($voucher->package)
            <div class="voucher-package">
                {{ $voucher->package->name }} - ৳{{ number_format($voucher->package->price) }}
            </div>
            @endif
            
            <div class="voucher-credentials">
                <div class="credential-row">
                    <span class="credential-label">ইউজারনেম</span>
                    <span class="credential-value">{{ $voucher->username }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">পাসওয়ার্ড</span>
                    <span class="credential-value">{{ $voucher->password }}</span>
                </div>
            </div>
            
            @if($voucher->package)
            <div class="voucher-info">
                সময়সীমা: {{ $voucher->package->validity }}
                @if($voucher->package->speed_limit)
                    | স্পীড: {{ $voucher->package->speed_limit }}
                @endif
            </div>
            @endif
            
            <div class="voucher-qr" id="qr-{{ $voucher->id }}"></div>
            
            <div class="voucher-footer">
                লগইন পেজে যান এবং ইউজারনেম ও পাসওয়ার্ড দিন<br>
                হোয়াটসঅ্যাপ: 01811871332
            </div>
        </div>
        @endforeach
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($vouchers as $voucher)
            new QRious({
                element: document.querySelector('#qr-{{ $voucher->id }}'),
                value: 'Username: {{ $voucher->username }}\nPassword: {{ $voucher->password }}',
                size: 80,
                level: 'M'
            });
            @endforeach
        });
    </script>
</body>
</html>
