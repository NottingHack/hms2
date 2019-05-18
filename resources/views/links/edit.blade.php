@extends('layouts.app')

@section('pageTitle', 'Edit Link')

@section('content')
<div class="container">
  <form role="form" method="POST" action="{{ route('links.update', $id) }}">
    @csrf
    @method('PATCH')
    @include ('links.partials.form', ['submitButtonText' => 'Update link'])
  </form>
</div>
@endsection
