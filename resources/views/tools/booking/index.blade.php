@extends('layouts.app')

@section('pageTitle', $tool->getName().' bookings')

@section('content')
<div class="container">
<p>To add a booking, click on any white area in the calendar below.</p>
</div>

<tool-calendar
    :tool-id="{{ $tool->getId() }}"
    bookings-url="{{ route('api.bookings.index', ['tool' => $tool->getId()]) }}"
    {{-- :initial-bookings="{{ json_encode($bookingsThisWeek) }}" --}}
    :user-can-book="{{ json_encode($userCanBook) }}"
    ></tool-calendar>
@endsection
