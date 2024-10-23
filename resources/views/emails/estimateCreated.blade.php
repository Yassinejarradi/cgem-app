<!DOCTYPE html>
<html>
<head>
    <title>Nouvelle demande créée</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            width: 80%;
            margin: auto;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            background-color: #f9f9f9;
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
            <h1>Nouvelle demande créée</h1>
        </div>
        <div class="content">
            <p class="important">Bonjour {{ $validator->name }},</p>
            <p>Vous avez une nouvelle demande à valider.</p>
            <p class="important">Numéro de la demande: {{ $estimate->estimate_number }}</p>
            <p class="important">Créé par: {{ $estimate->user->name }}</p>
            <p>Date de création: {{ \Carbon\Carbon::parse($estimate->estimate_date)->locale('fr')->isoFormat('LL') }}</p>
            <p>Veuillez examiner la demande à votre convenance en cliquant sur le lien ci-dessous:</p>
            <a href="{{ $estimateViewUrl }}" class="button">Voir la demande</a>
            <p>Merci.</p>
        </div>
        <div class="footer">
            <p>Ceci est un e-mail automatique, veuillez ne pas répondre.</p>
        </div>
    </div>
</body>
</html>
