<?php

namespace App\Mail;

use App\Models\Inscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class InscriptionValideeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $inscription;

    public function __construct(Inscription $inscription)
    {
        $this->inscription = $inscription;
    }

    public function build()
    {
        // Charger les relations nécessaires pour éviter les null
        $this->inscription->load([
            'etudiant.personne',
            'classe'
        ]);

        // Préparer les données pour la vue
        $etudiant = $this->inscription->etudiant;
        $personne = $etudiant->personne ?? null;

        return $this->subject('Votre inscription est validée')
                    ->view('emails.inscriptions.validee') // ou markdown si tu préfères
                    ->with([
                        'etudiantNom' => $personne->nom ?? 'Étudiant',
                        'etudiantPrenom' => $personne->prenom ?? '',
                        'classe' => $this->inscription->classe->libelle ?? 'Classe inconnue',
                        'annee' => $this->inscription->annee_academique ?? 'N/A',
                        'dateInscription' => Carbon::parse($this->inscription->date_inscription ?? now())->format('d/m/Y'),
                        'emailEtudiant' => $personne->email ?? 'inconnu@example.com',
                    ]);
    }
}
