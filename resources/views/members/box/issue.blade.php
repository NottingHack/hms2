@extends('layouts.app')

@section('pageTitle')
Confirm issuing a new box
@endsection

@section('content')
<div class="container">
  <p>You are about to issue a new box to {{ $boxUser->getFullname() }}</p>
  <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-success">
    <form action="{{ route('boxes.store') }}" method="POST" style="display: none">
      @csrf
      <input type="hidden" name="boxUser" value="{{ $boxUser->getId() }}">
    </form>
    <i class="fas fa-check" aria-hidden="true"></i> Confirm issue box
  </a>
</div>
@endsection
