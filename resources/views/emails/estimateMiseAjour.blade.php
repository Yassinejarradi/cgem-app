<!DOCTYPE html>
<html>
<head>
    <title>Notification de mise à jour de l'estimation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
        }
        .content {
            line-height: 1.6;
        }
        .content p {
            margin-bottom: 15px;
        }
        .content .important {
            font-size: 1.1em;
            font-weight: bold;
        }
        .button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            font-size: 0.9em;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mise à jour de l'estimation</h1>
        </div>
        <div class="content">
            <p class="important">Bonjour {{ $user->name }},</p>
            <p>Nous vous informons que votre estimation n°{{ $estimate->estimate_number }} a été mise à jour.</p>
            <p class="important">Statut actuel: {{ $estimate->status }}</p>
            <p>Date de mise à jour: {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('LL') }}</p>
            <p>Veuillez consulter les détails de votre estimation en cliquant sur le lien ci-dessous :</p>
            <a href="{{ $estimateViewUrl }}" class="button">Voir l'estimation</a>
            <p>Merci pour votre attention.</p>
        </div>
        <div class="footer">
            <p>Ceci est un e-mail automatique, veuillez ne pas répondre.</p>
        </div>
    </div>
</body>
</html>
