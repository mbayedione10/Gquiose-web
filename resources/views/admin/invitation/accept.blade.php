<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Activer votre compte - GquiOse</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 50%, #fcd34d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 440px;
            padding: 40px;
        }
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo img {
            height: 48px;
        }
        .logo-text {
            font-size: 28px;
            font-weight: 700;
            color: #f59e0b;
        }
        h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            text-align: center;
            margin-bottom: 8px;
        }
        .subtitle {
            color: #6b7280;
            text-align: center;
            margin-bottom: 32px;
            font-size: 14px;
        }
        .info-box {
            background: #fef3c7;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .info-box p {
            font-size: 14px;
            color: #92400e;
        }
        .info-box strong {
            color: #78350f;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }
        input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }
        input[readonly] {
            background: #f9fafb;
            color: #6b7280;
        }
        .error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 6px;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: #f59e0b;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover {
            background: #d97706;
        }
        .footer {
            text-align: center;
            margin-top: 24px;
            color: #9ca3af;
            font-size: 13px;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <span class="logo-text">GquiOse</span>
        </div>

        <h1>Activez votre compte</h1>
        <p class="subtitle">Bienvenue {{ $invitation->name }} ! Créez votre mot de passe pour accéder à l'administration.</p>

        <div class="info-box">
            <p><strong>Email :</strong> {{ $invitation->email }}</p>
            <p><strong>Rôle :</strong> {{ $invitation->role->name }}</p>
            <p><strong>Invité par :</strong> {{ $invitation->invitedBy->name }}</p>
        </div>

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.invitation.accept.submit', $invitation->token) }}">
            @csrf

            <div class="form-group">
                <label for="phone">Numéro de téléphone</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+224 XXX XXX XXX" required>
                @error('phone')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Minimum 8 caractères" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Retapez votre mot de passe" required>
            </div>

            <button type="submit" class="btn">Activer mon compte</button>
        </form>

        <p class="footer">En activant votre compte, vous acceptez les conditions d'utilisation de GquiOse.</p>
    </div>
</body>
</html>
