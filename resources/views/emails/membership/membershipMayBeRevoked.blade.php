@component('mail::message')
# Hello {{ $fullname }}

@content('emails.membership.membershipMayBeRevoked', 'main')

If you do wish to maintain your membership you will need to check your payments is setup as below and is equal or above the minimum amount of **@money($minimumAmount, 'GBP')**

@feature('standing_order_membership_payments')
Here are the details your standing order should have:

@component('mail::panel')
Account number: {{ $accountNo }}  
Sort Code: {{ $sortCode }}  
Reference: {{ $paymentRef }}  
Our Account Name: {{ $accountName }}
@endcomponent
@endfeature

If we receive a payment soon your membership will carry on unaffected.

@content('emails.membership.membershipMayBeRevoked', 'additional')

@if ($boxCount > 0)
Please also empty your members box.  
@endif

@if ($snackspaceBalance < 0)
We also request that you settle your snackspace balance of @money($snackspaceBalance, 'GBP')  
This can be paid off by card online in HMS @feature('cash_acceptors') or by cash in the space @endfeature  
@feature('standing_order_membership_payments')
Or via bank transfer using the reference **{{ $snackspaceRef }}** to the Account number and Sort Code above.  
@endfeature

@endif

Thanks,  
{{ config('branding.community_name') }} Membership Team
@endcomponent
