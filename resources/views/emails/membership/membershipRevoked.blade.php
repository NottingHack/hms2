@component('mail::message')
# Hello {{ $fullname }}

We are sorry to see you go but as you have not made a payment recently your Nottingham Hackspace membership has been revoked and your access to the space has been suspended.

If you do wish to reinstate your membership you will need to setup your standing order again.

Here are the details you need to set up a standing order:

@component('mail::panel')
Account number: {{ $accountNo }}<br>
Sort Code: {{ $sortCode }}<br>
Reference: {{ $paymentRef }}
@endcomponent

Once we've received your standing order (which may take 3-4 days to show up in our account after it leaves yours), your membership will be automatically reinstated.

Thanks,<br>
Nottinghack Membership Team
@endcomponent
