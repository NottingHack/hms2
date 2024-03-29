@component('mail::message')
# Hello {{ $fullname }}

As a member of {{ config('branding.space_name') }} you have made use of a chargeable tool, purchased a members box or used one of the Snackspace vending machines.  

**Our records show you have @money(-$snackspaceBalance, 'GBP') outstanding to pay**.  

You can view your full purchase/payment history using the 'Snackspace' tab on HMS or click below.
@component('mail::button', ['url' => route('snackspace.transactions.index')])
View your Snackspace history
@endcomponent

@content('emails.snackspace.current_member_debt', 'additional')
As of today the space is still owed **@money(-$latetsTotalDebt, 'GBP')**.  

## Payment Instructions
We can take online card payments via HMS @feature('cash_acceptors') or cash. Please use the cash acceptors in the members box storage room to pay off your snackspace debt. You can pay in notes and coins @endfeature.  

To pay off with a card, please log into HMS where you will see a button to "Add Money To Snackspace"  

@feature('cash_acceptors')
To use a cash acceptor, simply scan your membership card against the 'H' on the front, and then insert cash. The machine will record your payment and credit your account.  
If you have any issues with the acceptor, please let the snackspace team know.  
@endfeature

Thanks,  
{{ config('branding.space_name') }}
@endcomponent
