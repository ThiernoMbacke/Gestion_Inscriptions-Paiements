<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PersonneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // Liaison avec users - Crée automatiquement un user
            'user_id' => User::factory(),

            // Informations de base
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),

            // Coordonnées
            'email' => $this->faker->unique()->safeEmail(),
            'telephone' => $this->faker->phoneNumber(),

            // Détails personnels
            'date_de_naissance' => $this->faker->optional(70)->date(), // 70% de chance d'avoir une date
            'adresse' => $this->faker->address(),

            // Authentification
            'nom_d_utilisateur' => 'etud_' . Str::slug($this->faker->unique()->userName()),

            // Photo de profil
            'photo' => $this->faker->optional(30)->imageUrl(200, 200, 'people'), // 30% de chance d'avoir une photo

            // Timestamps (gérés automatiquement)
        ];
    }

    /**
     * Configure the factory (optional)
     */
    public function configure()
    {
        return $this->afterMaking(function ($personne) {
            // Logique après création
        });
    }

    /**
     * States (personnalisations)
     */
    public function avecPhoto()
    {
        return $this->state([
            'photo' => $this->faker->imageUrl(200, 200, 'people'),
        ]);
    }

    public function mineur()
    {
        return $this->state([
            'date_de_naissance' => $this->faker->dateTimeBetween('-17 years', '-13 years'),
        ]);
    }

    public function majeur()
    {
        return $this->state([
            'date_de_naissance' => $this->faker->dateTimeBetween('-80 years', '-18 years'),
        ]);
    }

    /**
     * State pour utiliser un user existant
     */
    public function forUser(User $user)
    {
        return $this->state([
            'user_id' => $user->id,
        ]);
    }
}
