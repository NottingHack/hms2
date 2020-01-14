@extends('layouts.app')

@section('pageTitle', 'Edit Label Template')

@section('content')
<div class="container">
  <form role="form" method="POST" action="{{ route('labels.update', $label->getTemplateName()) }}">
    @csrf
    @method('PATCH')
    @include ('labelTemplates.partials.form', ['submitButtonText' => 'Update template'])
  </form>
</div>
@endsection
