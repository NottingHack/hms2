@component('mail::message')
# Hello,

Thank you for registering your interest in {{ config('branding.space_name') }}.

If you'd like to become a {{ config('branding.space_name') }} member, the first step is is to:-
@component('mail::button', ['url' => route('register', $token)])
Create an HMS account
@endcomponent

Also, please add "{{ $membershipEmail }}" and "{{ $trusteesEmail }}" to your address book to reduce the chances of {{ config('branding.space_type') }} email going into your spam.

@content('emails.interestRegistered', 'main')

Here's the URL for the public Google Group:<br>
{{ $groupLink }}

Here are the {{ config('branding.space_type') }} rules:<br>
{{ $rulesLink }}

If you have any questions, just email.

Thanks,<br>
{{ config('branding.community_name') }} Membership Team
@endcomponent
