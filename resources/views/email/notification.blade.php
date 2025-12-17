<x-mail::message>
# {{ $greeting }}

Nous vous informons d'une nouvelle notification importante :

<x-mail::panel>
{!! nl2br(e($content)) !!}
</x-mail::panel>

Merci de votre attention et de votre engagement.

---

Cordialement,<br>
**L'Équipe {{ config('app.name') }}**
</x-mail::message>
