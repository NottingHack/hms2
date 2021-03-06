@component('mail::message')
# Hello {{ $fullname }},

<!-- TODO : update text copy  -->
Your details have now been approved by the Membership Team.

The next step in setting up your hackspace membership is for you to set up a bank standing order to Nottingham Hackspace using the details below.

As explained on your tour around the Hackspace, we have a "Pay what you think the space is worth to you" system. We are entirely member funded and rely almost exclusively on membership contributions to keep the Hackspace open.  
We recommend, if you are planning on using the space semi-regularly, that £15 is a fair monthly membership contribution.

From time to time all membership contributions will be reviewed and members may be asked to increase their contribution.  
Read more about the rules governing membership here: [Membership of the Hackspace](https://rules.nottinghack.org.uk/en/latest/membership-of-the-hackspace.html)

**Please note it is very important that you use the reference code exactly as provided.**

@component('mail::panel')
Account number: {{ $accountNo }}  
Sort Code: {{ $sortCode }}  
Reference: {{ $paymentRef }}  
Our Account Name: {{ $accountName }}
@endcomponent

Once we've received your first payment (which may take 3-4 days to show up in our account after it leaves yours), we'll send an email confirming membership. You can then collect your RFID card at a Wednesday Open Hack Night. You will then have 24 hour access to the space.

Thanks,  
Nottinghack Membership Team
@endcomponent
