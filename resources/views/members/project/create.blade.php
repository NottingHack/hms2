@extends('layouts.app')

@section('pageTitle', 'Add new project')

@section('content')
<div class="container">
  <form class="form-group" role="form" method="POST" action="{{ route('projects.store') }}">
    @csrf
    @include ('members.project.partials.form', ['submitButtonText' => 'Add project'])
  </form>
</div>
@endsection
