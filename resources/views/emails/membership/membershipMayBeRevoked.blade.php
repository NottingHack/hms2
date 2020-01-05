@component('mail::message')
# Hello {{ $fullname }}

We have not seen a membership payment from you recently. If we do not see one soon your membership to Nottingham Hackspace will be revoked.

If you do wish to maintain your membership you will need to check your standing order is setup as below.

Here are the details your standing order should have:

@component('mail::panel')
Account number: {{ $accountNo }}<br>
Sort Code: {{ $sortCode }}<br>
Reference: {{ $paymentRef }}
@endcomponent

If we receive a payment soon your membership will carry on unaffected.

If you no longer wish to be a member and have intentionally cancelled your standing order we are sorry to see you go, but would like to thank you for being a member and supporting the hackspace. Your membership will end in a couple of weeks time. Before your membership finally ends please ensure you remove any projects or materials you may have at the hackspace.

@if ($boxCount > 0)
Please also empty your members box.<br>
@endif

@if ($snackspaceBalance < 0)
We also request that you settle your snackspace balance of @format_pennies($snackspaceBalance)<br>
@endif

Thanks,<br>
Nottinghack Membership Team
@endcomponent
