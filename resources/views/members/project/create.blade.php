@extends('layouts.app')

@section('pageTitle', 'Add new project')

@section('content')
<form role="form" method="POST" action="{{ route('projects.store') }}">
{{ csrf_field() }}
@include ('members.project.partials.form', ['submitButtonText' => 'Add project'])
</form>
@endsection
