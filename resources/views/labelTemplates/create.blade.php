@extends('layouts.app')

@section('content')
<h2>Add New Label Template</h2>
<form role="form" method="POST" action="{{ route('labels.store') }}">
{{ csrf_field() }}
@include ('labelTemplates.partials.form', ['submitButtonText' => 'Add template'])
</form>
@endsection
