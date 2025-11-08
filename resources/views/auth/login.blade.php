<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-card">

            {{-- Logo + titre --}}
            <div class="login-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="login-logo">
                <h2>Connexion au Portail</h2>
            </div>

            @if(session('error'))
                <div class="error-message">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Mot de passe --}}
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input id="password" type="password" name="password" required>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Sélecteur de rôle --}}
                <div class="form-group">
                    <label for="role">Profil</label>
                    <select id="role" name="role" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="etudiant" {{ old('role') == 'etudiant' ? 'selected' : '' }}>Étudiant</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                        <option value="comptable" {{ old('role') == 'comptable' ? 'selected' : '' }}>Comptable</option>
                    </select>
                </div>

                {{-- Se souvenir de moi --}}
                <div class="form-group remember">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Se souvenir de moi</label>
                </div>

                {{-- Bouton --}}
                <button type="submit" class="btn-login">Se connecter</button>

                <p class="register-link">
                    Pas encore inscrit ? <a href="{{ route('register') }}">Créer un compte</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>