<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inscription Validée</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
            margin-top: 20px;
            border-radius: 5px;
        }
        .info-row {
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Inscription Validée</h1>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $etudiantPrenom }} {{ $etudiantNom }}</strong>,</p>

        <p>Nous avons le plaisir de vous informer que votre inscription a été <strong>validée avec succès</strong> !</p>

        <div class="info-row">
            <span class="label">Classe :</span>
            <span>{{ $classe }}</span>
        </div>

        <div class="info-row">
            <span class="label">Année académique :</span>
            <span>{{ $annee }}</span>
        </div>

        <div class="info-row">
            <span class="label">Date d'inscription :</span>
            <span>{{ $dateInscription }}</span>
        </div>

        <p>Votre parcours scolaire peut maintenant commencer. Nous vous souhaitons une excellente année académique !</p>

        <p>Cordialement,<br>
        L'équipe administrative</p>
    </div>
</body>
</html>
