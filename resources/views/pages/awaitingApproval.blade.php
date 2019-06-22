@extends('layouts.app')

@section('pageTitle', 'Awaiting approval')

@section('content')
<div class="container">
  <p>
    Your membership details are awaiting approval from our membership team.<br>
    They'll be giving your information a quick check, and if all is well they'll move your membership on to the next stage.<br>
    If there is an issue, will send you an email with details of what needs correcting.<br>
    This normally takes no more the 48 hours.
  </p>
  <p>
    <a class="btn btn-primary" href="{{ route('membership.edit', Auth::user()->getId()) }}">Update Details</a>
  </p>
</div>
@endsection
