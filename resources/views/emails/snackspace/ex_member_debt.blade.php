@component('mail::message')
# Hello {{ $fullname }}

During your time as a member of Nottingham Hackspace you made use of a chargeable tool, purchased a members box or used one of the Snackspace vending machines.

**Our records show you have @format_pennies(-$snackspaceBalance) outstanding to pay**.

You can view your full purchase/payment history using the 'Snackspace' tab on HMS or click below.
@component('mail::button', ['url' => route('snackspace.transactions.index')])
View your Snackspace history
@endcomponent

For the first eight years of Nottingham Hackspace, we allowed members up to £20 credit. However in 2019, this became unsustainable when the combined debt of all members, current and former, surpassed £2,500.

As of today the space is still owed **@format_pennies(-$latetsTotalDebt)** of which **@format_pennies(-$latetsExDebt)** is owed by former members like yourself.

The space is entirely funded by its members and they would greatly appreciate it if you could please pay off your outstanding balance.


## Payment Instructions
For former members we are allow payment by bank transfer as well as the cash acceptors in the space or online card payments via HMS.

If you wish to pay in cash please visit on a Wednesday Open Hack night and use the cash acceptors in the Members Storage room (someone from the Membership team will be around to give you access). You can pay in notes and coins.

To pay off with a card, please log into HMS where you will see a button to "Add Money To Snackspace"

To pay by bank transfer please use the details below.
It is very important to use this reference **exactly** so we can match this payment to you and your snackspace balance.
@component('mail::panel')
Account number: {{ $accountNo }}<br>
Sort Code: {{ $sortCode }}<br>
Reference: {{ $paymentRef }}
@endcomponent

Thanks,<br>
Nottingham Hackspace
@endcomponent
