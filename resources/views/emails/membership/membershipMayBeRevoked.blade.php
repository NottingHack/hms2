@component('mail::message')
# Hello {{ $fullname }}

We have not seen a membership payment from you recently. If we do not see one soon your membership to Nottingham Hackspace will be revoked.

If you do wish to maintain your membership you will need to check your standing order is setup as below.

Here are the details your standing order should have:

@component('mail::panel')
Account number: {{ $accountNo }}<br />
Sort Code: {{ $sortCode }}<br />
Reference: {{ $paymentRef }}
@endcomponent

If we receive a payment soon your membership will carry on unaffected.

Thanks,<br />
Nottinghack Membership Team
@endcomponent
