<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>{{ $title }}</title>

    <style>
        @page {
            margin: 24px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #1e293b;
        }

        .header {
            border-bottom: 2px solid #334155;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #0f172a;
        }

        .header p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 9px;
        }

        .summary {
            width: 100%;
            margin-bottom: 14px;
            border-spacing: 6px;
        }

        .summary td {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 8px;
            vertical-align: top;
        }

        .summary-label {
            color: #64748b;
            font-size: 8px;
        }

        .summary-value {
            margin-top: 3px;
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: #1e3a8a;
            color: white;
            border: 1px solid #1e40af;
            padding: 6px 4px;
            text-align: left;
            font-size: 8px;
        }

        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .data-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #64748b;
        }

        .footer {
            margin-top: 16px;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            font-size: 8px;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $title }}</h1>

        <p>{{ $subtitle }}</p>

        <p>
            Dicetak pada
            {{ now()->format('d-m-Y H:i') }}
        </p>
    </div>

    @if (count($summary) > 0)
        <table class="summary">
            <tr>
                @foreach ($summary as $label => $value)
                    <td>
                        <div class="summary-label">
                            {{ $label }}
                        </div>

                        <div class="summary-value">
                            {{ number_format((int) $value) }}
                        </div>
                    </td>
                @endforeach
            </tr>
        </table>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 22px;">No.</th>

                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @forelse ($rows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>

                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td
                        colspan="{{ count($headers) + 1 }}"
                        class="empty"
                    >
                        Tidak ada data pada periode yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen dibuat melalui Sistem Informasi Kesiswaan.
    </div>
</body>
</html>
