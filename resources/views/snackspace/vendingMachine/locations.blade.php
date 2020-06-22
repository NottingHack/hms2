@extends('layouts.app')

@section('pageTitle')
Locations for {{ $vendingMachine->getDescription() }}
@endsection

@section('content')
<div class="container">
  <p>Here you can set up the number and amount of products within each vending machine.</p>
</div>
<vending-locations
  assign-url="{{  route('api.snackspace.vending-machines.locations.assign', ['vendingMachine' => $vendingMachine->getID()]) }}"
  :initial-locations='@json($locations)'
  :products='@json($products)'
  ></vending-locations>
@endsection
