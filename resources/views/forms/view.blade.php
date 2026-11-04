@extends('layouts.app')

@section('content')

<div class="container">
<form-view :form-id="{{ $form->getId() }}" />
</div>

@endsection
