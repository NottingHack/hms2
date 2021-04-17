@extends('layouts.app')

@section('pageTitle', 'Privacy & Terms')

@section('content')
<div class="container">
  @content('pages.privacy_and_terms', 'main')
</div>
@endsection
