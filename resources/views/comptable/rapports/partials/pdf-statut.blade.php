<div class="info-box">
    <strong>Nombre de paiements :</strong> {{ count($data) }}
</div>

<table>
    <thead>
        <tr>
            <th>Matricule</th>
            <th>Nom & Prénom</th>
            <th>Classe</th>
            <th>Montant</th>
            <th>Mois</th>
            <th>Date</th>
            <th>Mode</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item['matricule'] }}</td>
            <td>{{ $item['nom'] }} {{ $item['prenom'] }}</td>
            <td>{{ $item['classe'] }}</td>
            <td class="text-right">{{ number_format($item['montant'], 0, ',', ' ') }} FCFA</td>
            <td>{{ $item['mois'] }}</td>
            <td>{{ $item['date_paiement'] }}</td>
            <td>{{ $item['mode_paiement'] }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="text-right">TOTAL</td>
            <td class="text-right">{{ number_format(collect($data)->sum('montant'), 0, ',', ' ') }} FCFA</td>
            <td colspan="3"></td>
        </tr>
    </tbody>
</table>
