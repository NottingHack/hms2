@component('mail::message')
# Hello {{ $fullname }},

One of the member admins has indicated that there is an issue with the contact details you entered, they have sent you the following message:

@component('mail::panel')
{{ $reason }}
@endcomponent

Please log into HMS and update your details.

@component('mail::button', ['url' => route('membership.edit', $userId)])
Update my Details
@endcomponent

Thanks,  
{{ config('branding.community_name') }} Membership Team
@endcomponent
