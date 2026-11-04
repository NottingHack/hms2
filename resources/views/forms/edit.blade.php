@extends('layouts.app')

@section('content')
@section('pageTitle', 'Edit ' . $form->getName())

<div class="container">
@include('forms.partials.form')
</div>

@endsection
