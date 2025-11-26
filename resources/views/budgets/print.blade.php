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
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 15mm 10mm;
        }

        .container {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Header */
        .header {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .header table {
            width: 100%;
            border: none;
        }

        .header td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .company-logo {
            width: 70px;
            height: 70px;
        }

        .company-name {
            font-size: 13pt;
            font-weight: bold;
            text-align: center;
        }

        .doc-info {
            text-align: right;
            font-size: 8pt;
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

        .info-section table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-section td {
            border: none;
            border-bottom: 1px solid #000;
            padding: 5px 8px;
        }

        .info-label {
            width: 150px;
            font-weight: bold;
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
            margin-top: 20px;
            width: 100%;
        }

        .signature-section table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-box {
            width: 25%;
            text-align: center;
            border: 1px solid #000;
            padding: 5px;
            height: 80px;
            vertical-align: top;
        }

        .signature-label {
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 40px;
            display: block;
        }

        .signature-name {
            font-size: 8pt;
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 40px;
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
            <table>
                <tr>
                    <td style="width: 80px;">
                        <div class="company-logo">
                            <!-- Logo placeholder -->
                            <div style="width: 70px; height: 70px; border: 2px solid #000; border-radius: 50%; text-align: center; line-height: 70px; font-size: 9pt;">
                                LOGO
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <div class="company-name">
                            PT. TIARA JAYA TUNGGAL MANDIRI
                        </div>
                    </td>
                    <td style="width: 150px;">
                        <div class="doc-info">
                            <div><strong>No:</strong> {{ $budget->request_no }}</div>
                            <div><strong>Tgl:</strong> {{ $budget->document_date->format('d/m/Y') }}</div>
                            <div><strong>Lampiran:</strong> 1</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Title -->
        <div class="title">BUKTI BANK/KAS KELUAR</div>
        <div class="subtitle">
            <span style="border: 2px solid #000; padding: 5px 30px; margin: 0 10px;">BANK</span>
            <span style="border: 2px solid #000; padding: 5px 30px; margin: 0 10px;">KAS</span>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <table>
                <tr>
                    <td class="info-label">Dibayarkan Kepada:</td>
                    <td>{{ $budget->user->name }}</td>
                </tr>
                @if($budget->project)
                <tr>
                    <td class="info-label">No. Project:</td>
                    <td>{{ $budget->project->no_project }}</td>
                </tr>
                <tr>
                    <td class="info-label">Nama Project:</td>
                    <td>{{ $budget->project->name }}</td>
                </tr>
                @endif
                <tr>
                    <td class="info-label">Nama Perusahaan:</td>
                    <td>PT. TJTM</td>
                </tr>
            </table>
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
                @php
                    $itemCount = count($budget->items);
                    $hasLongDescription = false;

                    // Check if any item has a long description (remarks with more than 100 characters)
                    foreach($budget->items as $item) {
                        if($item->remarks && strlen($item->remarks) > 100) {
                            $hasLongDescription = true;
                            break;
                        }
                    }

                    // Determine how many dummy rows to add
                    // If items are few (< 5) and no long descriptions, add dummy rows for formatting
                    $dummyRowsNeeded = 0;
                    if($itemCount < 5 && !$hasLongDescription) {
                        $dummyRowsNeeded = 5 - $itemCount;
                    }
                @endphp

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

                @if($dummyRowsNeeded > 0)
                    @for($i = 0; $i < $dummyRowsNeeded; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    @endfor
                @endif

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
            <table>
                <tr>
                    <td class="signature-box">
                        <span class="signature-label">Di Buat Oleh</span>
                        <div class="signature-name">
                            {{ $budget->user->name }}
                            <br><small style="font-size: 7pt;">{{ $budget->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </td>
                    <td class="signature-box">
                        <span class="signature-label">Mengetahui</span>
                        <div class="signature-name">
                            @php
                                $financeApproval = $budget->approvals->where('role', 'finance')->first();
                            @endphp
                            @if($financeApproval && $financeApproval->approver)
                                {{ $financeApproval->approver->name }}
                                <br><small style="font-size: 7pt;">{{ $financeApproval->approved_at ? $financeApproval->approved_at->format('d/m/Y H:i') : '' }}</small>
                            @else
                                _____________
                            @endif
                        </div>
                    </td>
                    <td class="signature-box">
                        <span class="signature-label">Menyetujui</span>
                        <div class="signature-name">
                            @php
                                $pmApproval = $budget->approvals->where('role', 'project_manager')->first();
                            @endphp
                            @if($pmApproval && $pmApproval->approver)
                                {{ $pmApproval->approver->name }}
                                <br><small style="font-size: 7pt;">{{ $pmApproval->approved_at ? $pmApproval->approved_at->format('d/m/Y H:i') : '' }}</small>
                            @else
                                _____________
                            @endif
                        </div>
                    </td>
                    <td class="signature-box">
                        <span class="signature-label">Kasir</span>
                        <div class="signature-name">
                            @php
                                $cashierApproval = $budget->approvals->where('role', 'cashier')->first();
                            @endphp
                            @if($cashierApproval && $cashierApproval->approver)
                                {{ $cashierApproval->approver->name }}
                                <br><small style="font-size: 7pt;">{{ $cashierApproval->approved_at ? $cashierApproval->approved_at->format('d/m/Y H:i') : '' }}</small>
                            @else
                                _____________
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        @if($budget->description)
        <div style="margin-top: 15px; padding: 8px; border: 1px solid #000;">
            <strong>Keterangan:</strong> {{ $budget->description }}
        </div>
        @endif
    </div>

</body>
</html>
