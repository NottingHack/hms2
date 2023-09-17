@extends('layouts.app')

@section('pageTitle', 'Temporary Access Bookings')

@section('content')
<div class="container">
  <p>To schedule temporary access for a User please select and drag on the calendar below.</p>
  <p>To re-schedule a booking, click and move the booking to a new time.</p>
  <p>To cancel a booking, click on the booking then confirm cancellation.</p>
</div>

@foreach ($buildings as $building)
<div class="container">
  <h2 id="{{ Str::slug($building->getName()) }}">{{ $building->getName() }}</h2>
  <hr>
  @if ($building->getAccessState() == HMS\Entities\Gatekeeper\BuildingAccessState::FULL_OPEN)
  <p>
    {{ $building->getName() }} is currently fully open, so the booking calendar is not shown.<br>
    This can be changed on the <a href="{{ route('gatekeeper.access-state.index') }}">Access State</a> page
  </p>
  @endif
</div>
@if ($building->getAccessState() != HMS\Entities\Gatekeeper\BuildingAccessState::FULL_OPEN)
<temporary-access
  :building='@json($building)'
  :bookable-areas='@json($building->getBookableAreas()->toArray())'
  :settings='@json($settings)'
  ></temporary-access>
  <br>
  <br>
@endif
@endforeach
@endsection
