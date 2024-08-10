@extends('layouts.app')

@section('pageTitle', 'Membership Graph')

@section('content')
<div class="container">
  <p></p>
  {!! $chartAll->container() !!}
</div>
@endsection

@push('scripts')
  {!! $chartAll->script() !!}
@endpush
