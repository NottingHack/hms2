@component('mail::message')
# Hello {{ $fullname }}

@content('emails.membership.membershipMayBeRevokedDueToUnderPayment', 'main')


If you do wish to maintain your membership you will need to check your payments are setup as below and is equal or above the minimum amount of **@money($minimumAmount, 'GBP')**  

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

@content('emails.membership.membershipMayBeRevokedDueToUnderPayment', 'additional')

@if ($boxCount > 0)
Please also empty your members box.  
@endif

@if ($snackspaceBalance < 0)
We also request that you settle your snackspace balance of @money($snackspaceBalance, 'GBP')  
This can be paid off by cash in the space or by card online in HMS  
Or via bank transfer using the reference **{{ $snackspaceRef }}** to the Account number ans Sort Code above.  
@endif

Thanks,  
{{ config('branding.community_name') }} Membership Team
@endcomponent
