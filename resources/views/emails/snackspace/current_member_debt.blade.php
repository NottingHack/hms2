@component('mail::message')
# Hello {{ $fullname }}

As a member of Nottingham Hackspace you have made use of a chargeable tool, purchased a members box or used one of the Snackspace vending machines.

**Our records show you have @money(-$snackspaceBalance, 'GBP') outstanding to pay**.

You can view your full purchase/payment history using the 'Snackspace' tab on HMS or click below.
@component('mail::button', ['url' => route('snackspace.transactions.index')])
View your Snackspace history
@endcomponent

For the first eight years of Nottingham Hackspace, we allowed members up to £20 credit. However in 2019, this became unsustainable when the combined debt of all members, current and former, surpassed £2,500.

In July 2019, that credit limit was reduced to zero. As of today the space is still owed **@money(-$latetsTotalDebt, 'GBP')**.


## Payment Instructions
We can take cash or online card payments (in HMS). Please use the cash acceptors in the members box storage room to pay off your snackspace debt. You can pay in notes and coins.

To use a cash acceptor, simply scan your membership card against the 'H' on the front, and then insert cash. The machine will record your payment and credit your account.<br>
If you have any issues with the acceptor, please let the snackspace team know via the #snackspace channel on slack.

To pay off with a card, please log into HMS where you will see a button to "Add Money To Snackspace"

Thanks,<br>
Nottingham Hackspace
@endcomponent
