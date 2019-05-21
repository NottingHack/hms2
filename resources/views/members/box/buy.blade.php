@extends('layouts.app')

@section('pageTitle')
Confirm buying a new box
@endsection

@section('content')
<div class="container">
  <p>You are about to buy a new box, this will debt your Snackspace account @format_pennies($boxCost)</p>
  <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-success">
  <form action="{{ route('boxes.store') }}" method="POST" style="display: none">
    @csrf
  </form>
  <i class="fas fa-check" aria-hidden="true"></i> Confirm buy box
  </a>
</div>
@endsection
