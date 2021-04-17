@component('mail::message')
# Hello {{ $fullname }},

Congratulations on your 18th Birthday.

This email is just to let you know your {{ config('branding.space_name') }} membership has been migrated from its Young Hacker status.

@content('emails.membership.eighteenCongratulations', 'additional')

Have a great day,<br>
{{ config('branding.community_name') }} Membership Team
@endcomponent
