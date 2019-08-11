@extends('layouts.app')

@section('pageTitle', 'Snackspace Debt')

@section('content')
<div class="container">
  <p></p>
  {!! $chartAll->container() !!}
@endsection

@push('scripts')
  {!! $chartAll->script() !!}
@endpush
