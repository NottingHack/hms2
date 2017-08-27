@component('mail::message')
# Hello Software Team,
I was unable to audit the following members automatically. <br />
These are current members that have no bank transactions against their name.


@component('mail::table')
| Member Id | Name           | Email            |
| --------- | -------------- | ---------------- |
@foreach ($ohCrapUsers as $user)
| {{ $user->getId() }} | [{{ $user->getFullName() }}]({{ route('users.show', $user->getId()) }}) | {{ $user->getEmail() }} |
@endforeach
@endcomponent

Please help me,<br />
HMS
@endcomponent
