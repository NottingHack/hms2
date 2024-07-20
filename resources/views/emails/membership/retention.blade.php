@component('mail::message')
# Hello {{ $fullname }},

@content('emails.membership.retention', 'main')

Thanks,

{{ config('branding.community_name') }} Membership Team
@endcomponent
