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

@if (count($formattedAwaitingUsersUnderMinimum))
The following awaiting payment members have sent a payment but it is under the minimum amount. They have been emailed.
@component('mail::table')
| Name                                       | Joint Account |
| ------------------------------------------ | ------------- |
@forelse ($formattedAwaitingUsersUnderMinimum as $user)
| [{{ $user['fullName'] }}]({{ route('users.admin.show', $user['id']) }}) | {{ $user['jointAccount'] }} |
@empty
@endforelse
@endcomponent
@endif

## Notified Members: {{ count($formattedWarnUsers) + count($formattedWarnUsersMinimumAmount) }}
We have not seen a payment from these members in a while, they may soon have their membership revoked.
@component('mail::table')
| Name                                       | Joint Account | Balance | Last payment date | Last visit date |
| ------------------------------------------ | ------------- | ------- | ----------------- | --------------- |
@forelse ($formattedWarnUsers as $user)
| [{{ $user['fullName'] }}]({{ route('users.admin.show', $user['id']) }}) | {{ $user['jointAccount'] }} | @money($user['balance'], 'GBP') | {{ $user['lastPaymentDate'] }} | {{ $user['lastVisitDate'] }} |
@empty
@endforelse
@endcomponent

We have seen a payment from these members but is it below the minimum, they may soon have their membership revoked.
@component('mail::table')
| Name                                       | Joint Account | Balance | Last payment date | Last visit date |
| ------------------------------------------ | ------------- | ------- | ----------------- | --------------- |
@forelse ($formattedWarnUsersMinimumAmount as $user)
| [{{ $user['fullName'] }}]({{ route('users.admin.show', $user['id']) }}) | {{ $user['jointAccount'] }} | @money($user['balance'], 'GBP') | {{ $user['lastPaymentDate'] }} | {{ $user['lastVisitDate'] }} |
@empty
@endforelse
@endcomponent

@if ($paymentNotificationsClearCount != 0)
### {{ $paymentNotificationsClearCount }} member {{ ($paymentNotificationsClearCount != 1) ? 's' : '' }} that had been previously notified {{ ($paymentNotificationsClearCount != 1) ? 'has' : 'have' }} now made a payment in time to not be revoked.
@endif


## Revoked Members: {{ count($formattedRevokeUsers) + count($formattedRevokeUsersMinimumAmount)}}
These members' last payment was too long ago, so their membership has been revoked.
@component('mail::table')
| Name                                       | Joint Account | Balance | Last payment date | Last visit date |
| ------------------------------------------ | ------------- | ------- | ----------------- | --------------- |
@forelse ($formattedRevokeUsers as $user)
| [{{ $user['fullName'] }}]({{ route('users.admin.show', $user['id']) }}) | {{ $user['jointAccount'] }} | @money($user['balance'], 'GBP') | {{ $user['lastPaymentDate'] }} | {{ $user['lastVisitDate'] }} |
@empty
@endforelse
@endcomponent

These members' last payment was below the minimum amount, so their membership has been revoked.
@component('mail::table')
| Name                                       | Joint Account | Balance | Last payment date | Last visit date |
| ------------------------------------------ | ------------- | ------- | ----------------- | --------------- |
@forelse ($formattedRevokeUsersMinimumAmount as $user)
| [{{ $user['fullName'] }}]({{ route('users.admin.show', $user['id']) }}) | {{ $user['jointAccount'] }} | @money($user['balance'], 'GBP') | {{ $user['lastPaymentDate'] }} | {{ $user['lastVisitDate'] }} |
@empty
@endforelse
@endcomponent


## Reinstated Members: {{ count($formattedReinstateUsers) }}
These ex-members have started paying again, so their membership has been reinstated.
@component('mail::table')
| Name                                       | Joint Account | Balance | Date made Ex | Last visit date |
| ------------------------------------------ | ------------- | ------- | ------------ | --------------- |
@forelse ($formattedReinstateUsers as $user)
| [{{ $user['fullName'] }}]({{ route('users.admin.show', $user['id']) }}) | {{ $user['jointAccount'] }} | @money($user['balance'], 'GBP') | {{ $user['dateMadeExMember'] }} | {{ $user['lastVisitDate'] }} |
@empty
@endforelse
@endcomponent

These ex-members have started paying again but it is under the minimum, so their membership has NOT been reinstated.
@component('mail::table')
| Name                                       | Joint Account | Balance | Date made Ex | Last visit date |
| ------------------------------------------ | ------------- | ------- | ------------ | --------------- |
@forelse ($formattedExUsersUnderMinimum as $user)
| [{{ $user['fullName'] }}]({{ route('users.admin.show', $user['id']) }}) | {{ $user['jointAccount'] }} | @money($user['balance'], 'GBP') | {{ $user['dateMadeExMember'] }} | {{ $user['lastVisitDate'] }} |
@empty
@endforelse
@endcomponent

Thank you,<br>
HMS
@endcomponent
