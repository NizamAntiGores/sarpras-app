<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Export PDF' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
            color: #1a365d;
        }

        .header .subtitle {
            font-size: 12px;
            color: #666;
        }

        .header .school-name {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
        }

        .info-box {
            background: #f7fafc;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #e2e8f0;
        }

        .info-box p {
            margin: 3px 0;
            font-size: 9px;
        }

        .info-box strong {
            color: #2d3748;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th {
            background: #2d3748;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        table td {
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
        }

        table tr:nth-child(even) {
            background: #f7fafc;
        }

        table tr:hover {
            background: #edf2f7;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-success {
            background: #c6f6d5;
            color: #22543d;
        }

        .badge-warning {
            background: #fefcbf;
            color: #744210;
        }

        .badge-danger {
            background: #fed7d7;
            color: #742a2a;
        }

        .badge-info {
            background: #bee3f8;
            color: #2a4365;
        }

        .badge-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #718096;
            padding: 10px;
            border-top: 1px solid #e2e8f0;
        }

        .page-break {
            page-break-after: always;
        }

        .summary-box {
            background: #ebf8ff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #90cdf4;
        }

        .summary-box h3 {
            font-size: 11px;
            color: #2b6cb0;
            margin-bottom: 5px;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 5px;
        }

        .summary-item .number {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
        }

        .summary-item .label {
            font-size: 8px;
            color: #718096;
        }

        .mono {
            font-family: 'DejaVu Sans Mono', monospace;
        }
    </style>
</head>

<body>
    <div class="header">
        <p class="school-name">SMK Negeri 1 Boyolangu</p>
        <h1>{{ $title ?? 'Laporan' }}</h1>
        <p class="subtitle">Digenerate pada: {{ $generatedAt }}</p>
    </div>

    @if(isset($filters) && count(array_filter($filters)) > 0)
        <div class="info-box">
            <p><strong>Filter yang diterapkan:</strong></p>
            @foreach($filters as $key => $value)
                @if($value)
                    <p>• {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</p>
                @endif
            @endforeach
        </div>
    @endif

    @yield('content')

    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh Sistem Manajemen Sarpras | {{ $generatedAt }}</p>
    </div>
</body>

</html>