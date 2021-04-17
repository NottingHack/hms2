@component('mail::message')
# Hello {{ $fullname }}

During your time as a member of Nottingham Hackspace you made use of a chargeable tool, purchased a members box or used one of the Snackspace vending machines.

**Our records show you have @money(-$snackspaceBalance, 'GBP') outstanding to pay**.

You can view your full purchase/payment history using the 'Snackspace' tab on HMS or click below.
@component('mail::button', ['url' => route('snackspace.transactions.index')])
View your Snackspace history
@endcomponent

@content('emails.snackspace.ex_member_debt', 'additional')

As of today the space is still owed **@money(-$latetsTotalDebt, 'GBP')** of which **@money(-$latetsExDebt, 'GBP')** is owed by former members like yourself.

The space is entirely funded by its members and they would greatly appreciate it if you could please pay off your outstanding balance.


## Payment Instructions
For former members we are allow payment by bank transfer as well as the cash acceptors in the space or online card payments via HMS.

If you wish to pay in cash please visit on a Wednesday Open Hack night and use the cash acceptors in the Members Storage room (someone from the Membership team will be around to give you access). You can pay in notes and coins.

To pay off with a card, please log into HMS where you will see a button to "Add Money To Snackspace"

To pay by bank transfer please use the details below.
It is very important to use this reference **exactly** so we can match this payment to you and your snackspace balance.
@component('mail::panel')
Account number: {{ $accountNo }}  
Sort Code: {{ $sortCode }}  
Reference: {{ $paymentRef }}  
Our Account Name: {{ $accountName }}
@endcomponent

Thanks,  
Nottingham Hackspace
@endcomponent
