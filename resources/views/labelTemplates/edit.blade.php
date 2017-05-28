@extends('layouts.app')

@section('pageTitle', 'Edit Label Template')

@section('content')
<form role="form" method="POST" action="{{ route('labels.update', $templateName) }}">
{{ csrf_field() }}
{{ method_field('PATCH') }}
@include ('labelTemplates.partials.form', ['submitButtonText' => 'Update template'])
</form>
@endsection
