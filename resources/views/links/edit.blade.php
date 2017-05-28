@extends('layouts.app')

@section('content')
<h2>Edit Link</h2>
<form role="form" method="POST" action="{{ route('links.update', $id) }}">
{{ csrf_field() }}
{{ method_field('PATCH') }}
@include ('links.partials.form', ['submitButtonText' => 'Update link'])
</form>
@endsection
