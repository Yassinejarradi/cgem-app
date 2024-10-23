<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Imprimer Demande d'achat</title>
    <link rel="stylesheet" href="{{ URL::to('assets/css/bootstrap.min.css') }}">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            position: relative;
        }
        .blue-line {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: 10px;
            background-color: #1F3A93; /* Adjust this color to match the logo */
            z-index: 1;
        }
        .content-container {
            margin-left: 20px; /* Space to avoid overlapping with the blue line */
            padding: 20px;
            background-color: #fff;
            z-index: 2;
            position: relative;
        }
        .inv-logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .text-right {
            text-align: right;
        }
        .text-uppercase {
            text-transform: uppercase;
        }
        .table-responsive {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #dee2e6;
        }
        th {
            background-color: #f8f9fa;
        }
        .page-break {
            page-break-before: always;
        }
        .title {
            margin-top: 10px;
            padding: 10px;
            color: gray;
            text-align: center;
            border-radius: 5px;
        }
        @media print {
            .no-print {
                display: none;
            }
            .blue-line {
                height: 100vh; /* Ensures the blue line covers the entire height */
            }
        }
    </style>
</head>
<body>
    <div class="blue-line"></div>
    <div class="content-container">
        <div class="container">
            <div class="title">
                <h1>{{ $estimate->type_demande === 'fourniture' ? 'Demande de Fourniture' : 'Demande d\'Achat' }}</h1>
            </div>
            <div class="row mb-4">
                <div class="col-sm-6">
                    <img src="{{ URL::to('assets/img/logo2.png') }}" class="inv-logo" alt="Logo">
                    <ul class="list-unstyled">
                        <li><strong>Nom:</strong> {{ $estimate->user->name }}</li>
                        <li><strong>Département:</strong> {{ $estimate->user->department }}</li>
                    </ul>
                </div>
                <div class="col-sm-6 text-right">
                    <h3 class="text-uppercase">#{{ $estimate->estimate_number }}</h3>
                    <ul class="list-unstyled">
                        <li><strong>Date de création:</strong> {{ \Carbon\Carbon::parse($estimate->estimate_date)->locale('fr')->isoFormat('LL') }}</li>
                        <li><strong>Date souhaitée:</strong> {{ \Carbon\Carbon::parse($estimate->expiry_date)->locale('fr')->isoFormat('LL') }}</li>
                    </ul>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Article</th>
                            <th>Description</th>
                            <th>Quantité</th>
                            <th>Motif de demande</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($estimatesJoin as $key => $item)
                            @if ($key > 0 && $key % 20 == 0) <!-- Adjust the number to fit your page size -->
                                </tbody></table><div class="page-break"></div><table class="table table-striped table-hover"><thead><tr><th>#</th><th>Article</th><th>Description</th><th>Quantité</th><th>Motif de demande</th></tr></thead><tbody>
                            @endif
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $item->item }}</td>
                                <td style="white-space: pre-wrap;">{{ $item->description }}</td>
                                <td>{{ $item->qty }}</td>
                                <td style="white-space: pre-wrap;">{{ $item->motif }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($estimate->type_demande === 'achat' && $estimateDetails->isNotEmpty())
                <hr>
                <h4 class="mb-4">Détails de la demande:</h4>
                @foreach ($estimateDetails as $type => $details)
                    <p><strong>{{ $type }}:</strong> {{ implode(', ', $details->pluck('detail_value')->toArray()) }}</p>
                @endforeach
                <hr>
            @endif

            <h4>Actions par les validateurs</h4>
            @foreach ($estimateActions as $action)
                <p>
                    La demande a été {{ $action->action === 'validated' ? 'validée' : 'refusée' }} par {{ $action->user->name }}, 
                    le {{ \Carbon\Carbon::parse($action->created_at)->locale('fr')->isoFormat('LL à HH:mm:ss') }}.
                </p>
            @endforeach

            @if ($acheteurActions->isNotEmpty())
                <hr>
                <h4>Actions prises par l'acheteur</h4>
                @foreach ($acheteurActions as $acheteurAction)
                    <p>
                        Action: {{ $acheteurAction->action }} par {{ $acheteurAction->acheteur->name }},
                        le {{ \Carbon\Carbon::parse($acheteurAction->created_at)->locale('fr')->isoFormat('LL à HH:mm:ss') }}.
                    </p>
                @endforeach
            @endif
        </div>
    </div>
</body>
</html>
