<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();

            // Utilisation de clés étrangères avec le bon type (unsignedBigInteger)
            $table->unsignedBigInteger('etudiant_id');
            $table->unsignedBigInteger('classe_id');
            $table->unsignedBigInteger('administration_id')->nullable(); // Rendue nullable car pas dans le diagramme

            // Champs avec types appropriés
            $table->string('annee_academique');
            $table->date('date_inscription');

            // Colonne statut
            $table->enum('statut', ['en_attente', 'validee', 'rejetee', 'annulee'])
                  ->default('en_attente');

            // Ajout du champ pour le suivi de l'email de confirmation
            $table->boolean('confirmation_envoyee')->default(false);

            $table->timestamps();

            // Contraintes de clé étrangère
            $table->foreign('etudiant_id')->references('id')->on('etudiants')->onDelete('cascade');
            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('administration_id')->references('id')->on('administrations')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inscriptions');
    }
};
