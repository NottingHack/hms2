@component('mail::message')
# Hello {{ $teamName }},


I have audited the members with following results.<br>
Each will have had an automated email.

## New Members: {{ count($formattedApproveUsers) }}
We have just seen a payment from these new members.
@component('mail::table')
| Name                                       | Pin  | Joint Account |
| ------------------------------------------ | ---- | ------------- |
@forelse ($formattedApproveUsers as $user)
| [{{ $user['fullName'] }}]({{ route('users.admin.show', $user['id']) }}) | {{ $user['pin'] }} | {{ $user['jointAccount'] }} |
@empty
@endforelse
@endcomponent


## Notified Members: {{ count($formattedWarnUsers) }}
We have not seen a payment from these members in a while, they may soon have their membership revoked.
@component('mail::table')
| Name                                       | Joint Account | Balance | Last payment date | Last visit date |
| ------------------------------------------ | ------------- | ------- | ----------------- | --------------- |
@forelse ($formattedWarnUsers as $user)
| [{{ $user['fullName'] }}]({{ route('users.admin.show', $user['id']) }}) | {{ $user['jointAccount'] }} | @format_pennies($user['balance']) | {{ $user['lastPaymentDate'] }} | {{ $user['lastVisitDate'] }} |
@empty
@endforelse
@endcomponent

@if ($paymentNotificationsClearCount != 0)
### {{ $paymentNotificationsClearCount }}} member {{ ($paymentNotificationsClearCount != 1) ? 's' : '' }} that had been previously notified {{ ($paymentNotificationsClearCount != 1) ? 'has' : 'have' }} now made a payment in time to not be revoked.
@endif


## Revoked Members: {{ count($formattedRevokeUsers) }}
These members' last payment was too long ago, so their membership has been revoked
@component('mail::table')
| Name                                       | Joint Account | Balance | Last payment date | Last visit date |
| ------------------------------------------ | ------------- | ------- | ----------------- | --------------- |
@forelse ($formattedRevokeUsers as $user)
| [{{ $user['fullName'] }}]({{ route('users.admin.show', $user['id']) }}) | {{ $user['jointAccount'] }} | @format_pennies($user['balance']) | {{ $user['lastPaymentDate'] }} | {{ $user['lastVisitDate'] }} |
@empty
@endforelse
@endcomponent


## Reinstated Members: {{ count($formattedReinstateUsers) }}
These ex-members have started paying again, so their membership has been reinstated
@component('mail::table')
| Name                                       | Joint Account | Balance | Date made Ex | Last visit date |
| ------------------------------------------ | ------------- | ------- | ------------ | --------------- |
@forelse ($formattedReinstateUsers as $user)
| [{{ $user['fullName'] }}]({{ route('users.admin.show', $user['id']) }}) | {{ $user['email'] }} | {{ $user['jointAccount'] }} | @format_pennies($user['balance']) | {{ $user['dateMadeExMember'] }} | {{ $user['lastVisitDate'] }} |
@empty
@endforelse
@endcomponent

Thank you,<br>
HMS
@endcomponent
