@extends('layouts.app')

@section('pageTitle', 'Register a new extension')

@section('content')
<div class="container">
  @include ('phones.partials.extension-form', ['submitButtonText' => 'Register Extension'])
</div>
@endsection
