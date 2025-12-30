<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }} - G Qui Ose</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-yellow: #fec900;
            --dark: #1d1d1b;
            --white: #fff;
        }
        body {
            background: linear-gradient(135deg, var(--dark) 0%, #2d2d2b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
        }
        .card-header {
            background: var(--primary-yellow);
            border-bottom: none;
            padding: 30px 30px 20px;
            text-align: center;
        }
        .card-header h2 {
            color: var(--dark);
            font-weight: 700;
            margin-bottom: 0;
            margin-top: 15px;
        }
        .logo-container {
            display: flex;
            justify-content: center;
        }
        .logo-container img {
            width: 120px;
            height: 120px;
        }
        .card-body {
            background: var(--white);
            padding: 30px;
        }
        .info-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid var(--primary-yellow);
        }
        .info-section h5 {
            color: var(--dark);
            font-weight: 600;
            margin-bottom: 15px;
        }
        .info-section ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
        .info-section li {
            margin-bottom: 8px;
            color: #555;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: var(--primary-yellow);
            box-shadow: 0 0 0 3px rgba(254, 201, 0, 0.2);
        }
        .btn-delete {
            background: var(--dark);
            color: var(--white);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-delete:hover {
            background: #333;
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        .footer-text {
            text-align: center;
            color: #888;
            font-size: 13px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card mx-auto">
            <div class="card-header">
                <div class="logo-container">
                    <img src="{{ asset('images/logo_gquiose.svg') }}" alt="G Qui Ose">
                </div>
                <h2>Suppression de compte</h2>
            </div>
            <div class="card-body">
                @if(Session::has('message'))
                    <div class="alert {{ Session::get('error') ? 'alert-danger' : 'alert-success' }} mb-4">
                        {{ Session::get('message') }}
                    </div>
                @endif

                <div class="info-section">
                    <h5>Que se passe-t-il lorsque vous supprimez votre compte ?</h5>
                    <ul>
                        <li>Votre profil et vos informations personnelles seront supprimés</li>
                        <li>Vos alertes et signalements seront supprimés</li>
                        <li>Vos réponses aux questions seront supprimées</li>
                        <li>Cette action est <strong>irréversible</strong></li>
                    </ul>
                </div>

                <form action="{{ route('remove.account') }}" method="post">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold">
                            Adresse email associée au compte <span class="text-danger">*</span>
                        </label>
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email"
                            id="email"
                            placeholder="votre@email.com"
                            value="{{ old('email') }}"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-delete">
                        Supprimer mon compte
                    </button>
                </form>

                <p class="footer-text">
                    En supprimant votre compte, vous acceptez que toutes vos données soient définitivement effacées conformément à notre politique de confidentialité.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
