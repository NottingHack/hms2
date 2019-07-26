@extends('layouts.app')

@section('pageTitle', 'Snackspace Debt')

@section('content')
<div class="container">
  <p>For all time</p>
  <div class=row>
    {!! $chartAll->container() !!}
  </div>
  <p>Last year</p>
  <div class=row>
    {!! $recent->container() !!}
  </div>
</div>
@endsection

@push('scripts')
  {!! $chartAll->script() !!}
  {!! $recent->script() !!}
@endpush
