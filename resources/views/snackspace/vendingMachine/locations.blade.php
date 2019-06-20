@extends('layouts.app')

@section('pageTitle')
Locations for {{ $vendingMachine->getDescription() }}
@endsection

@section('content')
<div class="container">
  <p>Words about how to setup products per location</p>
</div>
<vending-locations
  assign-url="{{  route('api.snackspace.vending-machines.locations.assign', ['vendingMachine' => $vendingMachine->getID()]) }}"
  :initial-locations="{{ json_encode($locations) }}"
  :products="{{ json_encode($products) }}"
  ></vending-locations>
@endsection
