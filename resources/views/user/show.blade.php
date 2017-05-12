@extends('layouts.app')

@section('content')
<h1>{{ $user->getFullname() }}</h1>

@endsection
