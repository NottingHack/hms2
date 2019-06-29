@component('mail::message')
# Hello {{ $fullname }},

<!-- TODO : update text copy  -->
Your details have now been approved by the Membership Team.

The next step in setting up your hackspace membership is for you to set up a bank standing order to Nottingham Hackspace using the details below.

Please note it is very important that you use the reference code exactly as provided.

@component('mail::panel')
Account number: {{ $accountNo }}<br>
Sort Code: {{ $sortCode }}<br>
Reference: {{ $paymentRef }}
@endcomponent

Once we've received your first payment (which may take 3-4 days to show up in our account after it leaves yours), we'll send an email confirming membership. You can then collect your RFID card at a Wednesday Open Hack Night. You will then have 24 hour access to the space.

Thanks,<br>
Nottinghack Membership Team
@endcomponent
