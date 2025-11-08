<div class="info-box">
    <strong>Nombre d'étudiants :</strong> {{ count($data) }}
</div>

<table>
    <thead>
        <tr>
            <th>Matricule</th>
            <th>Nom & Prénom</th>
            <th>Classe</th>
            <th>Total Payé</th>
            <th>Nb Paiements</th>
            <th>Dernier Paiement</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item['matricule'] }}</td>
            <td>{{ $item['nom'] }} {{ $item['prenom'] }}</td>
            <td>{{ $item['classe'] }}</td>
            <td class="text-right">{{ $item['total_paye'] }}</td>
            <td class="text-center">{{ $item['nombre_paiements'] }}</td>
            <td>{{ $item['dernier_paiement'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
