<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>

    <link rel="stylesheet" href="{{ asset('frontend/style.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>

<div>
    <div class="contact-form-wrapper d-flex justify-content-center">

        @if(\Illuminate\Support\Facades\Session::has('message'))
           @if(\Illuminate\Support\Facades\Session::get('error') == 'true')
               <p class="alert alert-danger">
                   {{ \Illuminate\Support\Facades\Session::get('message') }}
               </p>
            @else
                <p class="alert alert-success">
                    {{ \Illuminate\Support\Facades\Session::get('message') }}
                </p>
           @endif
        @endif

        <form action="{{ route('remove.account') }}" method="post" class="contact-form">
            @csrf
            <h5 class="title">Suppression de compte</h5>
            <p class="description">Vous pouvez facilement supprimer toutes les données de votre compte
            </p>
            <div>
                <label for="email"> Courriel <span style="color: red">*</span></label>
                <input type="text" class="form-control rounded border-white mb-3 form-input" name="email" id="name"
                       placeholder="Courrriel" required>
            </div>

            <div class="submit-button-wrapper">
                <input type="submit" value="Supprimez">
            </div>
        </form>
    </div>
</div>

</body>
</html>
