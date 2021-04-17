@component('mail::message')
# Hello {{ $fullname }},

@content('emails.membership.approved', 'main')

**Please note it is very important that you use the reference code exactly as provided.**

@component('mail::panel')
Account number: {{ $accountNo }}  
Sort Code: {{ $sortCode }}  
Reference: {{ $paymentRef }}  
Our Account Name: {{ $accountName }}
@endcomponent

@content('emails.membership.approved', 'additional')

Thanks,  
{{ config('branding.community_name') }} Membership Team
@endcomponent
