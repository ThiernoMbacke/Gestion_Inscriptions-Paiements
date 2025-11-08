<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titre }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #007bff;
        }

        .header h1 {
            color: #007bff;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 10px;
        }

        .info-box {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background-color: #007bff;
            color: white;
        }

        table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .total-row {
            background-color: #e9ecef !important;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-danger { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titre }}</h1>
        <p>Généré le {{ $date_generation }}</p>
    </div>

    @if($type == 'mois')
        @include('comptable.rapports.partials.pdf-mois', ['data' => $data])
    @elseif($type == 'classe')
        @include('comptable.rapports.partials.pdf-classe', ['data' => $data])
    @elseif($type == 'statut')
        @include('comptable.rapports.partials.pdf-statut', ['data' => $data])
    @elseif($type == 'mode')
        @include('comptable.rapports.partials.pdf-mode', ['data' => $data])
    @endif

    <div class="footer">
        <p>Document confidentiel - {{ config('app.name') }} © {{ date('Y') }}</p>
    </div>
</body>
</html>
