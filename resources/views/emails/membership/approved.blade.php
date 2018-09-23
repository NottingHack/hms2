@component('mail::message')
# Hello {{ $fullname }},

<!-- TODO : update text copy  -->
Your details have now been approved by the Membership Team. The next step in setting up you hackspace membership to for you to setup a standing order to Nottingham Hackspace using your unique reference.

Here are the details you need to set up a standing order:

@component('mail::panel')
Account number: {{ $accountNo }}<br>
Sort Code: {{ $sortCode }}<br>
Reference: {{ $paymentRef }}
@endcomponent

Once we've received your first standing order (which may take 3-4 days to show up in our account after it leaves yours), you'll be sent details to arrange a meeting with someone from the membership team. After that meeting, you'll have 24hr access to the space.

Thanks,<br>
Nottinghack Membership Team
@endcomponent
