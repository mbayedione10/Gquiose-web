@component('mail::message')
# Invitation à rejoindre l'équipe GquiOse

Bonjour **{{ $invitation->name }}**,

Vous avez été invité(e) par **{{ $invitation->invitedBy->name }}** à rejoindre l'équipe d'administration de GquiOse en tant que **{{ $invitation->role->name }}**.

Pour activer votre compte, veuillez cliquer sur le bouton ci-dessous et créer votre mot de passe :

@component('mail::button', ['url' => $activationUrl, 'color' => 'primary'])
Activer mon compte
@endcomponent

<small>Ce lien d'invitation expire le **{{ $invitation->expires_at->format('d/m/Y à H:i') }}**. Passé ce délai, vous devrez demander une nouvelle invitation.</small>

---

Si vous n'attendiez pas cette invitation, vous pouvez ignorer cet email en toute sécurité.

Cordialement,<br>
**L'Équipe {{ config('app.name') }}**
@endcomponent
