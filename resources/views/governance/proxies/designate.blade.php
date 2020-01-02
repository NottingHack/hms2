@extends('layouts.app')

@section('pageTitle')
Proxy request for {{ $meeting->getTitle() }}
@endsection

@section('content')
<div class="container">
  <p>
    Another member has ask you to represent them at an upcoming Meeting by acting as their Proxy.<br>
    Before accepting be sure you will be at the meeting yourself.<br>
    If the meeting will include items to vote on make sure to talk over your opinions and how you wish your proxy votes to be cast.<br>
    Most votes, including Resolutions to change the Constitution, are asked as a Yes/No/Abstain decision.
  </p>
  <hr>
  <form role="form" method="POST" action="{{ route('governance.proxies.store', ['meeting' => $meeting->getId()]) }}">
    @csrf
    <input type="hidden" name="principal_id" value="{{ $principal->getId() }}">

    <div class="form-group">
      <input type="text" readonly class="form-control-plaintext font-weight-bold @error('principal_id') is-invalid @enderror" value="{{ $principal->getFullname() }} has ask you to be their Proxy for the meeting on {{ $meeting->getStartTime()->toFormattedDateString() }}">
      @error('principal_id')
      <span class="invalid-feedback" role="alert">
        <strong>{{ $errors->first('principal_id') }}</strong>
      </span>
      @enderror
    </div>
    <div class="form-group form-check">
      <input id="proxy" class="form-check-input @error('proxy') is-invalid @enderror" type="checkbox" name="proxy" {{ old('proxy', 0) ? 'checked="checked"' : '' }}>
      <label class="form-check-label" for="proxy">
        Do you accept this Proxy?
      </label>
      @error('proxy')
      <span class="invalid-feedback" role="alert">
        <strong>{{ $errors->first('proxy') }}</strong>
      </span>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-check"></i> Accept Proxy</button>
  </form>
</div>
@endsection
