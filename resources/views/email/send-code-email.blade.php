@component('mail::message')
# {{ $title }}

{{ $content }}

@component('mail::panel')
<div style="text-align: center;">
<span style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #3d4852;">{{ $code }}</span>
</div>
@endcomponent

<small>Ce code expire dans **10 minutes**. Si vous n'avez pas demandé ce code, vous pouvez ignorer cet email en toute sécurité.</small>

---

Cordialement,<br>
**L'Équipe {{ config('app.name') }}**
@endcomponent
