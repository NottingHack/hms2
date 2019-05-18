@extends('layouts.app')

@section('pageTitle', 'Add new project')

@section('content')
<div class="container">
  <form role="form" method="POST" action="{{ route('projects.update', $project->getId()) }}">
    @csrf
    {{ method_field('PATCH') }}
    @include ('members.project.partials.form', ['submitButtonText' => 'Update project'])
  </form>
</div>
@endsection
