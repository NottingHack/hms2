@component('mail::message')
# Hello {{ $fullname }}

As a member of Nottingham Hackspace you have made use of a chargeable tool, purchased a members box or used one of the Snackspace vending machines.

**Our records show you have @format_pennies(-$snackspaceBalance) outstanding to pay**.

You can view your full purchase/payment history using the 'Snackspace' tab on HMS or click below.
@component('mail::button', ['url' => route('snackspace.transactions.index')])
View your Snackspace history
@endcomponent

For the first 8 years of Nottinghack we allowed members up to £20 credit, however in 2019 this became unsustainable when the combined debt of all Members, current and ex, surpassed £2,500. In July 2019 that credit limit was reduced to zero. As of today the space is still owed **@format_pennies(-$latetsTotalDebt)**.


## Payment Instructions
At present we can only take cash payments, please use the cash acceptors in the members box storage room to pay your snackspace credit

To use a cash acceptor, simply scan your membership card against the 'H' on the front, and then insert cash. The machine will record your payment and credit your account.<br>
If you have any issues with the acceptor, please let the snackspace team know via the #snackspace channel on slack.

Thanks,<br>
Nottingham Hackspace
@endcomponent
