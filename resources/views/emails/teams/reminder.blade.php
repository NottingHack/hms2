@component('mail::message')
# Hello {{ $teamName }}!

@content('emails.teams.reminder', 'main')  

Thank you,  
{{ config('branding.community_name') }} Trustees
@endcomponent
