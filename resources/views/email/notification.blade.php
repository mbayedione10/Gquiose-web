<x-mail::message>
# {{ $greeting }}

Bonjour,

Nous vous informons d'une nouvelle notification importante :

<x-mail::panel>
{{ $content }}
</x-mail::panel>

Merci de votre attention et de votre engagement.

---

Cordialement,<br>
**L'Équipe {{ config('app.name') }}**
</x-mail::message>
