@component('mail::message')
# Hello,

Thank you for registering your interest in {{ config('branding.space_name') }}.  

If you'd like to become a {{ config('branding.space_name') }} member, the first step is is to:-
@component('mail::button', ['url' => route('register', $token)])
Create an HMS account
@endcomponent

Also, please add "{{ $membershipEmail }}" and "{{ $trusteesEmail }}" to your address book to reduce the chances of future {{ config('branding.space_type') }} emails going into your spam.  

@content('emails.interestRegistered', 'main')  

@if ($groupLink)
Here's the URL for the public Google Group:  
{{ $groupLink }}
@endif

@if ($discordHTML) 
Discord is used for members to chat online. You can join the {{ config('branding.space_type') }} Discord at:  
{{ $discordHTML }}  
@endif

Here are the {{ config('branding.space_type') }} rules:  
{{ $rulesLink }}  

If you have any questions, just email.  

Thanks,  
{{ config('branding.community_name') }} Membership Team
@endcomponent
