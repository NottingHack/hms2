@extends('layouts.app')

@section('pageTitle', $tool->getDisplayName().' bookings')

@section('content')
<div class="container">
  <p>To add a booking, select and drag on the calendar below.</p>
  <p>To re-schedule the booking, click and move the booking to a new time.</p>
  <p>To cancel a booking, click on the booking then confirm cancellation.</p>
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
