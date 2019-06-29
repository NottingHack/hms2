@extends('layouts.app')

@section('pageTitle', 'Awaiting approval')

@section('content')
<div class="container">
  <p>
    Your membership details are awaiting approval from our Membership Team.</p>
<p> They'll be giving your information a quick check, and if all is well they'll move your membership on to the next stage.</p>
<p> If there are any queries, they will send you an email with more information.</p>
<p>This normally takes no more than 48 hours.
  </p>
  <p>
    <a class="btn btn-primary" href="{{ route('membership.edit', Auth::user()->getId()) }}">Update Details</a>
  </p>
</div>
@endsection
