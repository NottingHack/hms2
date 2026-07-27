@extends('layouts.app')

@section('pageTitle', 'Edit Extension')

@section('content')
<div class="container">
  @include ('phones.partials.extension-form', ['submitButtonText' => 'Update Extension'])
</div>
@endsection
