@component('mail::message')
# Hello Software Team,
I was unable to audit the following members automatically. <br>
These {{ count($ohCrapUsers) }} are current members that have no bank transactions against their name.


@component('mail::table')
| Member Id | Name           | Email            |
| --------- | -------------- | ---------------- |
@foreach ($ohCrapUsers as $user)
| {{ $user->getId() }} | [{{ $user->getFullname() }}]({{ route('users.admin.show', $user->getId()) }}) | {{ $user->getEmail() }} |
@endforeach
@endcomponent

Please help me,<br>
HMS
@endcomponent
