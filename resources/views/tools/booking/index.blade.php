@extends('layouts.app')

@section('pageTitle', $tool->getName().' bookings')

@section('content')
<div class="container">
  <p>To add a booking, select and drag on the calendar below.</p>
  <p>To re-schedule the booking, click and move the booking to a new time.</p>
  <p>To cancel a booking, click on the booking then confirm cancellation.</p>
  <p>Each type of booking is represented by a different colour.<br><span class="btn btn-booking-normal btn-sm mb-1">Normal</span> <span class="btn btn-booking-induction btn-sm mb-1">Induction</span> <spann class="btn btn-booking-maintenance btn-sm mb-1">Maintenance</spann></p>
</div>

<tool-calendar
    :tool-id="{{ $tool->getId() }}"
    :booking-length-max="{{ $tool->getLengthMax() }}"
    :bookings-max="{{ $tool->getBookingsMax() }}"
    bookings-url="{{ route('api.bookings.index', ['tool' => $tool->getId()]) }}"
    {{-- :initial-bookings="{{ json_encode($bookingsThisWeek) }}" --}}
    :user-can-book="{{ json_encode($userCanBook) }}"
    ></tool-calendar>
@endsection
