@extends('layouts.app')

@section('content')
<h1>{{ $user->getFullName() }}</h1>

@endsection
