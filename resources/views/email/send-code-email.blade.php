@component('mail::message')
    # {{ $title }}

    {{ $content }}

    ## Confirmation code: {{ $code }}

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
