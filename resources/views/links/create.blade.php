@extends('layouts.app')

@section('content')
<h2>Add New Link</h2>
<form role="form" method="POST" action="{{ route('links.store') }}">
{{ csrf_field() }}
@include ('links.partials.form', ['submitButtonText' => 'Add link'])
</form>
@endsection
