@component('mail::message')
# Hello,

Thank you for registering your interest in Nottingham Hackspace.

If you'd like to become a Nottingham Hackspace member, the first step is is to:-
@component('mail::button', ['url' => route('register', $token)])
Create an HMS account
@endcomponent

Also, please add "{{ $membershipEmail }}" and "{{ $trusteesEmail }}" to your address book to reduce the chances of hackspace email going into your spam.

@content('emails.interestRegistered', 'main')

Here's the URL for the public Google Group:<br>
{{ $groupLink }}

Here are the hackspace rules:<br>
{{ $rulesLink }}

If you have any questions, just email.

Thanks,<br>
Nottinghack Membership Team
@endcomponent
