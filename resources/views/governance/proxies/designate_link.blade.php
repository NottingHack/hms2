@extends('layouts.app')

@section('pageTitle')
Designate a Proxy for {{ $meeting->getTitle() }}
@endsection

@section('content')
<div class="container">
  @isset($proxy)
  <p>
    Currently <strong>{{ $proxy->getProxy()->getFullname() }}</strong> is acting as your Proxy for this meeting.<br>
    If you wish to have someone else act as you proxy just follow the steps below.n And when a new Proxy is accepted the old one will be automaticly cancled.<br>
    Your Proxy can be cancelled by clicking below.
  </p>
  <a href="javascript:void(0);" onclick="$(this).find('form').submit();"  class="btn btn-danger btn-block">
    <form action="{{ route('governance.proxies.destroy', ['meeting' => $meeting->getId()]) }}" method="POST" style="display: none">
      @method('DELETE')
      @csrf
    </form>
    <i class="fas fa-trash" aria-hidden="true"></i> Cancel Proxy
  </a>
  <hr>
  @endisset
  <p>
    If you are not going to be able to make the forthcoming meeting on {{ $meeting->getStartTime()->toFormattedDateString() }}, please consider designating another member to act as you proxy.<br>

    <ol>
      <li>Find a fellow member going to the AGM.</li>
      <li>Ask them if they will be you Proxy.</li>
      <li>Email, Slack or SMS them the link below. Hint click (<button type="button" class="btn btn-light btn-sm" onclick="copyToClipboard('#designate-link')"><i class="far fa-copy"></i></button>) to copy it to your clipboard.</li>
      <li>Once your Proxy follows the link and confirms their acceptance you will receive confirmation via email.</li>
    </ol>
  </p>
  <p>
  <span class="align-middle">
    <strong><span id="designate-link">{{ URL::temporarySignedRoute('governance.proxies.designate', now()->addMonths(2), ['meeting' => $meeting->getId(), 'principal' => $user->getId()]) }}</span></strong>&nbsp;
    <button type="button" class="btn btn-light btn-sm" onclick="copyToClipboard('#designate-link')"><i class="far fa-copy"></i></button>
  </span>
  </p>
  <p>
    If the meeting will include items to vote on make sure to talk over your opinions and how you wish your proxy votes to be cast.<br>
    Most votes, including Resolutions to change the Constitution, are asked as a Yes/No/Abstain decision.
  </p>
  <p>
    If you do make it to the meeting your proxy vote will be automatically cancelled when you Check-in
  </p>
</div>
@endsection
