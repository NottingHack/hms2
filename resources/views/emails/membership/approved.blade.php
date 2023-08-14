@component('mail::message')
# Hello {{ $fullname }},

@content('emails.membership.approved', 'main')

@feature('standing_order_membership_payments')
**Please note it is very important that you use the reference code exactly as provided.**

@component('mail::panel')
Account number: {{ $accountNo }}  
Sort Code: {{ $sortCode }}  
Reference: {{ $paymentRef }}  
Our Account Name: {{ $accountName }}
@endcomponent
@endfeature

@content('emails.membership.approved', 'additional')

Thanks,  
{{ config('branding.community_name') }} Membership Team
@endcomponent
