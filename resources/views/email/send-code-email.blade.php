@component('mail::message')
    # {{ $title }}

    {{ $content }}

    ## Confirmation code: {{ $code }}

    L'Équipe,<br>
    {{ config('app.name') }}
@endcomponent
