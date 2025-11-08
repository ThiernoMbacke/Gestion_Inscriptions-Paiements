<?php

namespace App\Http\Controllers;
use App\Models\Personne;
use App\Models\Comptable;
use App\Models\Etudiant;
use App\Models\Administration;
use Illuminate\Http\Request;
use App\Traits\GenerateApiResponse;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;  // Ajouté pour Auth
use Exception;

class UserController extends Controller
{
    use GenerateApiResponse;

    public function index()
{
    try {
        // Récupérer tous les utilisateurs avec leurs relations
        $users = User::with(['personne.etudiant', 'personne.comptable', 'personne.administration'])->get();

        // Retourner la vue avec les données
        return view('administration.utilisateurs.index', compact('users'));

    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Erreur lors de la récupération: ' . $e->getMessage());
    }
}



    public function update(Request $request, $id)
{
    try {
        $user = User::with('personne')->findOrFail($id);

        // Validation
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'nom_d_utilisateur' => 'required|string|max:50|unique:personnes,nom_d_utilisateur,' . $user->personne->id,
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'date_de_naissance' => 'nullable|date',
            'adresse' => 'nullable|string',
            'cni' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:etudiant,admin,comptable'
        ]);

        // Mise à jour User
        $user->name = $validated['nom'] . ' ' . $validated['prenom'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        // Mise à jour Personne
        $personne = $user->personne;
        $personne->nom = $validated['nom'];
        $personne->prenom = $validated['prenom'];
        $personne->nom_d_utilisateur = $validated['nom_d_utilisateur'];
        $personne->telephone = $validated['telephone'] ?? null;
        $personne->date_de_naissance = $validated['date_de_naissance'] ?? null;
        $personne->adresse = $validated['adresse'] ?? null;
        $personne->cni = $validated['cni'] ?? null;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $personne->photo = $path;
        }
        $personne->save();

        return redirect()->route('administration.utilisateurs.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    } catch (Exception $e) {
        return redirect()->route('administration.utilisateurs.index')
            ->with('error', 'Mise à jour échouée : ' . $e->getMessage());
    }
}

public function edit($id)
{
    try {
        $user = User::with('personne')->findOrFail($id); // si User est lié à Personne
        return view('administration.utilisateurs.edit', compact('user'));
    } catch (Exception $e) {
        return redirect()->route('administration.utilisateurs.index')
            ->with('error', 'Utilisateur introuvable.');
    }
}


    public function destroy($id)
{
    try {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('administration.utilisateurs.index')
            ->with('success', 'Suppression réussie');
    } catch (Exception $e) {
        return redirect()->route('administration.utilisateurs.index')
            ->with('error', 'Suppression échouée : ' . $e->getMessage());
    }
}


   public function show($id)
{
    try {
        $user = User::with([
            'personne.etudiant',
            'personne.comptable',
            'personne.administration'
        ])->findOrFail($id);

        return view('administration.utilisateurs.show', compact('user'));
    } catch (\Exception $e) {
        return redirect()->route('administration.utilisateurs.index')
                         ->withErrors('Utilisateur non trouvé');
    }
}



    public function getformdetails()
    {
        try {
            return $this->successResponse([], 'Données du formulaire récupérées avec succès');
        } catch (Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des données du formulaire', 500, $e->getMessage());
        }
    }

   public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
        'role' => 'required|string',
    ]);

    try {
        // Tentative de connexion avec Auth::attempt
        $credentials = $request->only(['email', 'password']);

        if (Auth::attempt($credentials)) {
            // Vérification du rôle après connexion
            $user = Auth::user();
            if ($user->role === $request->role) {
                // Redirection selon le rôle
                switch($user->role) {
                    case 'admin':
                        return redirect('/administration/dashboard');
                    case 'etudiant':
                        return redirect('/etudiant/dashboard');
                    case 'comptable':
                        return redirect('/comptable/dashboard');
                    default:
                        return redirect('/dashboard');
                }
            } else {
                Auth::logout();
                return back()->with('error', 'Rôle incorrect.');
            }
        }

        return back()->with('error', 'Email ou mot de passe incorrect.');

    } catch (Exception $e) {
        return back()->with('error', 'Erreur lors de la connexion: ' . $e->getMessage());
    }
}

    public function logout(Request $request)
    {
        try {
            if ($request->user() && method_exists($request->user(), 'currentAccessToken')) {
                // Tentative de suppression du token si disponible (API)
                $token = $request->user()->currentAccessToken();
                if ($token) {
                    $token->delete();
                }

                return response()->json([
                    'status_code' => 200,
                    'status_message' => 'Déconnexion réussie'
                ]);
            } else {
                // Déconnexion web classique
                Auth::guard('web')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/');
            }
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'status_message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'nom' => 'required|string|max:100',
        'prenom' => 'required|string|max:100',
        'nom_d_utilisateur' => 'required|string|max:50|unique:personnes,nom_d_utilisateur',
        'email' => 'required|email|max:150|unique:users,email',
        'telephone' => 'nullable|string|max:20',
        'date_de_naissance' => 'nullable|date',
        'adresse' => 'nullable|string',
        'password' => 'required|string|min:6|confirmed',
        'role' => ['required', Rule::in(['etudiant', 'admin', 'comptable'])],
        'photo' => 'nullable|image|max:2048',
    ]);

    // Créer le User
    $user = new User();
    $user->name = $validated['nom'] . ' ' . $validated['prenom']; // ou selon ce que vous voulez dans name
    $user->email = $validated['email'];
    $user->role = $validated['role'];
    $user->password = bcrypt($validated['password']);
    $user->save();

    // Créer la Personne liée au User
    $personne = new Personne();
    $personne->user_id = $user->id;
    $personne->nom = $validated['nom'];
    $personne->prenom = $validated['prenom'];
    $personne->nom_d_utilisateur = $validated['nom_d_utilisateur'];
    $personne->email = $validated['email'];
    $personne->telephone = $validated['telephone'] ?? null;
    $personne->date_de_naissance = $validated['date_de_naissance'] ?? null;
    $personne->adresse = $validated['adresse'] ?? null;

    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('photos', 'public');
        $personne->photo = $path;
    }
    $personne->save();

    // Gestion spécifique selon rôle
    if ($validated['role'] === 'etudiant') {
        $currentYear = date('Y');

        $lastEtudiant = Etudiant::where('matricule', 'like', "$currentYear-ETU-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEtudiant && $lastEtudiant->matricule) {
            $lastNumber = (int)substr($lastEtudiant->matricule, strlen($currentYear) + 5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $matricule = $currentYear . '-ETU-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        Etudiant::create([
            'personne_id' => $personne->id,
            'matricule' => $matricule,
            'accepte_email' => 0,
        ]);
    } elseif ($validated['role'] === 'comptable') {
        Comptable::create([
            'personne_id' => $personne->id,
        ]);
    } elseif ($validated['role'] === 'admin') {
        Administration::create([
            'personne_id' => $personne->id,
        ]);
    }

    return redirect()->route('administration.utilisateurs.index')
        ->with('success', 'Utilisateur créé avec succès.');
}

public function create()
{
    return view('administration.utilisateurs.create');
}

}
