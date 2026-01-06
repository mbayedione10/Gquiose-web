<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation invalide - GquiOse</title>
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
            text-align: center;
        }
        .logo {
            margin-bottom: 32px;
        }
        .logo-text {
            font-size: 28px;
            font-weight: 700;
            color: #f59e0b;
        }
        .icon {
            width: 80px;
            height: 80px;
            background: #fef2f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        .icon svg {
            width: 40px;
            height: 40px;
            color: #dc2626;
        }
        h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
        }
        .message {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: #f59e0b;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn:hover {
            background: #d97706;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <span class="logo-text">GquiOse</span>
        </div>

        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>

        <h1>Invitation invalide</h1>
        <p class="message">{{ $message }}</p>

        <a href="/admin/login" class="btn">Aller à la connexion</a>
    </div>
</body>
</html>
