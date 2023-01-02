@component('mail::message')
# Hello {{ $fullname }}

@content('emails.membership.membershipRevokedDueToUnderPayment', 'main')


@if ($boxCount > 0)
Our records show that you have left a members box at the space please arrange to collect it on a Wednesday Open Hack Night.  
@endif

@if ($snackspaceBalance < 0)
We request that you settle your snackspace balance of @money($snackspaceBalance, 'GBP')  
This can be paid off by cash in the space or by card online in HMS  
Or via bank transfer using the reference **{{ $snackspaceRef }}** to the Account number ans Sort Code below.  
@endif

If you do wish to reinstate your membership you will need to increase your payments to equal or above the minimum amount of **@money($minimumAmount, 'GBP')**  

@feature('standing_order_membership_payments')
Here are the details you need to set up a standing order:

@component('mail::panel')
Account number: {{ $accountNo }}  
Sort Code: {{ $sortCode }}  
Reference: {{ $paymentRef }}  
Our Account Name: {{ $accountName }}
@endcomponent
@endfeature

Once we've received your payment (which may take 3-4 days to show up in our account after it leaves yours), your membership will be automatically reinstated.

Thanks,  
{{ config('branding.community_name') }} Membership Team
@endcomponent
