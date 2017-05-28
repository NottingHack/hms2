@extends('layouts.app')

@section('pageTitle', 'Edit Link')
@section('content')
<form role="form" method="POST" action="{{ route('links.update', $id) }}">
{{ csrf_field() }}
{{ method_field('PATCH') }}
@include ('links.partials.form', ['submitButtonText' => 'Update link'])
</form>
@endsection
