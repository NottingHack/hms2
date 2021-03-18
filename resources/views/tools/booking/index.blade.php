@extends('layouts.app')

@section('pageTitle', $tool->getDisplayName().' bookings')

@section('content')
<div class="container">
  @content('tools.booking.index', 'directions')
  <p>Each type of booking is represented by a different colour.<br><span class="btn btn-booking-normal btn-sm mb-1">Normal</span> <span class="btn btn-booking-induction btn-sm mb-1">Induction</span> <span class="btn btn-booking-maintenance btn-sm mb-1">Maintenance</span></p>
</div>

<tool-calendar
    :tool-id="{{ $tool->getId() }}"
    :tool-restricted="{{ $tool->isRestricted() ? 'true' : 'false' }}"
    :booking-length-max="{{ $tool->getLengthMax() }}"
    :bookings-max="{{ $tool->getBookingsMax() }}"
    bookings-url="{{ route('api.tools.bookings.index', ['tool' => $tool->getId()]) }}"
    {{-- :initial-bookings='@json($bookingsThisWeek)' --}}
    :user-can-book='@json($userCanBook)'
    ></tool-calendar>
@endsection
