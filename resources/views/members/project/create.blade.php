@extends('layouts.app')

@section('pageTitle', 'Add new project')

@section('content')
<div class="container">
  <p>Words about creating a project and printing a DNH label</p>
  <form class="form-group" role="form" method="POST" action="{{ route('projects.store') }}">
    @csrf
    @include ('members.project.partials.form', ['submitButtonText' => 'Add project'])
  </form>
</div>
@endsection
