<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('personnes', function (Blueprint $table) {
            $table->id();

            // Liaison avec users
            $table->unsignedBigInteger('user_id')->unique();

            // Informations de base
            $table->string('nom', 100);
            $table->string('prenom', 100);

            // Coordonnées
            $table->string('email', 150)->unique();
            $table->string('telephone', 20)->nullable();

            // Détails personnels
            $table->date('date_de_naissance')->nullable();
            $table->text('adresse')->nullable();

            // Authentification
            $table->string('nom_d_utilisateur', 50)->unique();

            // Photo de profil
            $table->string('photo')->nullable();

            // Timestamps
            $table->timestamps();

            // Index
            $table->index(['nom', 'prenom']);
            $table->index('email');
            $table->index('nom_d_utilisateur');

            // Clé étrangère vers users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('personnes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('personnes');
    }
};
