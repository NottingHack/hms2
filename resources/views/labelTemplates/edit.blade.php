@extends('layouts.app')

@section('content')
<h2>Edit Label Template</h2>
<form role="form" method="POST" action="{{ route('labels.update', $templateName) }}">
{{ csrf_field() }}
{{ method_field('PATCH') }}
@include ('labelTemplates.partials.form', ['submitButtonText' => 'Update template'])
</form>
@endsection
