<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Bank/Kas Keluar - {{ $budget->request_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
        }

        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 5mm;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .company-logo {
            width: 80px;
            height: 80px;
        }

        .company-name {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            flex: 1;
        }

        .doc-info {
            text-align: right;
            font-size: 9pt;
        }

        .doc-info div {
            margin-bottom: 2px;
        }

        /* Title */
        .title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 15px 0;
            letter-spacing: 2px;
        }

        .subtitle {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 15px;
        }

        /* Info Section */
        .info-section {
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
            padding: 3px 0;
        }

        .info-label {
            width: 150px;
            font-weight: bold;
        }

        .info-value {
            flex: 1;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th {
            background-color: #f0f0f0;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }

        td {
            padding: 8px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 23%;
            text-align: center;
            border: 1px solid #000;
            padding: 5px;
            min-height: 80px;
        }

        .signature-label {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 50px;
        }

        .signature-name {
            font-size: 9pt;
            border-top: 1px solid #000;
            padding-top: 3px;
        }

        /* Print styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .container {
                width: 100%;
                max-width: none;
            }

            @page {
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-logo">
                <!-- Logo placeholder - you can add your company logo here -->
                <div style="width: 80px; height: 80px; border: 2px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10pt; text-align: center;">
                    LOGO
                </div>
            </div>
            <div class="company-name">
                PT. TIARA JAYA TUNGGAL MANDIRI
            </div>
            <div class="doc-info">
                <div><strong>No:</strong> {{ $budget->request_no }}</div>
                <div><strong>Tgl:</strong> {{ $budget->document_date->format('d/m/Y') }}</div>
                <div><strong>Lampiran:</strong> 1</div>
            </div>
        </div>

        <!-- Title -->
        <div class="title">BUKTI BANK/KAS KELUAR</div>
        <div class="subtitle">
            <span style="border: 2px solid #000; padding: 5px 30px; margin: 0 10px;">BANK</span>
            <span style="border: 2px solid #000; padding: 5px 30px; margin: 0 10px;">KAS</span>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Dibayarkan Kepada:</div>
                <div class="info-value">{{ $budget->user->name }}</div>
            </div>

            @if($budget->project)
            <div class="info-row">
                <div class="info-label">No. Project:</div>
                <div class="info-value">{{ $budget->project->no_project }}</div>
            </div>

            <div class="info-row">
                <div class="info-label">Nama Project:</div>
                <div class="info-value">{{ $budget->project->name }}</div>
            </div>
            @endif

            <div class="info-row">
                <div class="info-label">Nama Perusahaan:</div>
                <div class="info-value">PT. TJTM</div>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">QTY</th>
                    <th style="width: 60%;">Uraian</th>
                    <th style="width: 32%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budget->items as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $item->account ? $item->account->account_description : '-' }}</strong>
                        @if($item->remarks)
                        <br><span style="font-size: 9pt;">{{ $item->remarks }}</span>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach

                <!-- Empty rows for formatting -->
                @for($i = count($budget->items); $i < 8; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                @endfor

                <!-- Additional Info Row -->
                <tr>
                    <td colspan="2">
                        <div style="display: flex; justify-content: space-between;">
                            <div><strong>CH/B.G. No.</strong></div>
                            <div><strong>Tgl. JT.</strong></div>
                        </div>
                    </td>
                    <td>&nbsp;</td>
                </tr>

                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="2" style="text-align: right; padding-right: 20px;">
                        <strong>Total:</strong>
                    </td>
                    <td class="text-right">
                        <strong>{{ number_format($budget->total_amount, 0, ',', '.') }}</strong>
                    </td>
                </tr>

                <!-- Terbilang Row -->
                <tr>
                    <td colspan="3" style="padding: 8px; font-style: italic; border-top: 2px solid #000;">
                        <strong>Terbilang:</strong> {{ terbilang($budget->total_amount) }} Rupiah
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Transfer Info -->
        @if($budget->accountFrom || $budget->account_to)
        <div style="margin: 10px 0; padding: 5px; border: 1px solid #000;">
            @if($budget->accountFrom)
            <strong>Transfer Dari:</strong> {{ $budget->accountFrom->bank_name }} - {{ $budget->accountFrom->account_number }}
            (a/n {{ $budget->accountFrom->account_holder_name }})
            @endif
            @if($budget->account_to)
            <br><strong>Transfer Ke:</strong> {{ $budget->account_to }}
            @endif
        </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-label">Di Buat Oleh</div>
                <div class="signature-name">
                    {{ $budget->user->name }}
                    <br><small style="font-size: 8pt;">{{ $budget->created_at->format('d/m/Y H:i') }}</small>
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-label">Mengetahui</div>
                <div class="signature-name">
                    @php
                        $financeApproval = $budget->approvals->where('role', 'finance')->first();
                    @endphp
                    @if($financeApproval && $financeApproval->approver)
                        {{ $financeApproval->approver->name }}
                        <br><small style="font-size: 8pt;">{{ $financeApproval->approved_at ? $financeApproval->approved_at->format('d/m/Y H:i') : '' }}</small>
                    @else
                        _____________
                    @endif
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-label">Menyetujui</div>
                <div class="signature-name">
                    @php
                        $pmApproval = $budget->approvals->where('role', 'project_manager')->first();
                    @endphp
                    @if($pmApproval && $pmApproval->approver)
                        {{ $pmApproval->approver->name }}
                        <br><small style="font-size: 8pt;">{{ $pmApproval->approved_at ? $pmApproval->approved_at->format('d/m/Y H:i') : '' }}</small>
                    @else
                        _____________
                    @endif
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-label">Kasir</div>
                 <div class="signature-name">
                    @php
                        $cashierApproval = $budget->approvals->where('role', 'cashier')->first();
                    @endphp
                    @if($cashierApproval && $cashierApproval->approver)
                        {{ $cashierApproval->approver->name }}
                        <br><small style="font-size: 8pt;">{{ $cashierApproval->approved_at ? $cashierApproval->approved_at->format('d/m/Y H:i') : '' }}</small>
                    @else
                        _____________
                    @endif
                </div>
            </div>
        </div>

        @if($budget->description)
        <div style="margin-top: 15px; padding: 8px; border: 1px solid #000;">
            <strong>Keterangan:</strong> {{ $budget->description }}
        </div>
        @endif
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
