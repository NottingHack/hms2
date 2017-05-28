@extends('layouts.app')

@section('pageTitle', 'Add New Link')

@section('content')
<form role="form" method="POST" action="{{ route('links.store') }}">
{{ csrf_field() }}
@include ('links.partials.form', ['submitButtonText' => 'Add link'])
</form>
@endsection
