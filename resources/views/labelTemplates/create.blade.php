@extends('layouts.app')

@section('pageTitle', 'Add New Label Template')

@section('content')
<form role="form" method="POST" action="{{ route('labels.store') }}">
{{ csrf_field() }}
@include ('labelTemplates.partials.form', ['submitButtonText' => 'Add template'])
</form>
@endsection
