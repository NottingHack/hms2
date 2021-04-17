@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-sm-6">
      <div class="card  text-center">
        <h5 class="card-header">Gatekeeper</h5>
        <div class="card-body">
          @content('gatekeeper.have_left', 'card')
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
