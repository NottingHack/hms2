@extends('layouts.app')

@section('pageTitle', 'Add New Link')

@section('content')
<div class="container">
  <form role="form" method="POST" action="{{ route('links.store') }}">
    {{ csrf_field() }}
    @include ('links.partials.form', ['submitButtonText' => 'Add link'])
  </form>
</div>
@endsection
