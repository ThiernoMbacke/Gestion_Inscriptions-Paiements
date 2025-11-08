<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();

            // Clés étrangères correctement typées
            $table->unsignedBigInteger('inscription_id');
            $table->unsignedBigInteger('comptable_id')->nullable();

            // Types de données spécifiques
            $table->date('date_paiement');
            $table->decimal('montant', 10, 2); // 10 chiffres au total, 2 après la virgule
            $table->string('reference_transaction')->unique();

            // Mois à payer
            $table->enum('mois_a_payer', [
                'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin',
                'juillet', 'aout', 'septembre', 'octobre', 'novembre', 'decembre'
            ]);

            // Enums pour les choix prédéfinis
            $table->enum('mode_paiement', ['espece', 'virement', 'wave', 'orange_money']);
            $table->enum('statut', ['en_attente', 'valide', 'rejete', 'annule'])->default('en_attente');

            // Suivi de l'email de validation
            $table->boolean('validation_email_envoye')->default(false);

            $table->timestamps();

            // Contraintes de clés étrangères
            $table->foreign('inscription_id')
                  ->references('id')
                  ->on('inscriptions')
                  ->onDelete('cascade');

            $table->foreign('comptable_id')
                  ->references('id')
                  ->on('administrations')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('paiements');
    }
};
