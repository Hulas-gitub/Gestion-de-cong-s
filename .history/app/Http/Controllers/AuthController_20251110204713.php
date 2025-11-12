public function login(Request $request)
{
    // ✅ Support du JSON envoyé par fetch()
    $data = $request->isJson() ? $request->json()->all() : $request->all();

    // Validation des données
    $validator = \Validator::make($data, [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ], [
        'email.required' => 'Veuillez saisir votre adresse email',
        'email.email' => 'Veuillez utiliser une adresse email valide',
        'password.required' => 'Veuillez saisir votre mot de passe',
        'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
        ], 422);
    }

    try {
        // Récupérer les valeurs validées
        $email = $data['email'];
        $password = $data['password'];
        $remember = $data['remember'] ?? false;

        // Rechercher l'utilisateur actif avec son rôle
        $user = User::with('role')
            ->where('email', $email)
            ->where('actif', true)
            ->first();

        Log::info('Tentative de connexion', [
            'email' => $email,
            'user_found' => $user ? 'oui' : 'non'
        ]);

        if (!$user) {
            Log::warning('Utilisateur non trouvé ou inactif', ['email' => $email]);
            return response()->json([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect'
            ], 401);
        }

        if (!Hash::check($password, $user->password)) {
            Log::warning('Mot de passe incorrect', ['email' => $email]);
            return response()->json([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect'
            ], 401);
        }

        if (!$user->role) {
            Log::error('Utilisateur sans rôle', ['user_id' => $user->id_user]);
            return response()->json([
                'success' => false,
                'message' => 'Votre compte n\'a pas de rôle assigné. Contactez l\'administrateur.'
            ], 403);
        }

        // Authentifier et enregistrer la session
        Auth::login($user, $remember);

        Session::put('user_id', $user->id_user);
        Session::put('user_name', $user->prenom . ' ' . $user->nom);
        Session::put('user_role', $user->role->nom_role);
        Session::put('user_role_id', $user->role->id_role);
        Session::put('user_email', $user->email);
        Session::put('user_matricule', $user->matricule);
        Session::put('departement_id', $user->departement_id);

        Log::info('Connexion réussie', [
            'user_id' => $user->id_user,
            'role' => $user->role->nom_role
        ]);

        // Déterminer l'URL de redirection selon le rôle
        $redirectUrl = $this->getRedirectUrlByRole($user);

        return response()->json([
            'success' => true,
            'message' => "Bienvenue {$user->prenom} {$user->nom} ! Connexion réussie",
            'redirect' => $redirectUrl,
            'user' => [
                'id' => $user->id_user,
                'name' => $user->prenom . ' ' . $user->nom,
                'email' => $user->email,
                'role' => $user->role->nom_role,
                'role_id' => $user->role->id_role,
                'matricule' => $user->matricule,
                'departement_id' => $user->departement_id,
            ]
        ], 200);

    } catch (\Exception $e) {
        Log::error('Erreur lors de la connexion', [
            'email' => $data['email'] ?? null,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Une erreur s\'est produite lors de la connexion'
        ], 500);
    }
}
