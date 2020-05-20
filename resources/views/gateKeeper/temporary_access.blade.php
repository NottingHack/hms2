@extends('layouts.app')

@section('pageTitle', 'Temporary Access Bookings')

@section('content')
<temporary-access
    :booking-length-max="{{ Carbon\CarbonInterval::instance(new DateInterval(Meta::get('temp_access_reset_interval', 'PT12H')))->totalMinutes }}"
    ></temporary-access>
@endsection
