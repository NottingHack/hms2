@extends('layouts.app')

@section('pageTitle', 'Access Codes')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-sm">
      <div class="card">
        <div class="card-header icon-card-body">
          <div class="icon-card-icon"><i class="fas fa-key" aria-hidden="true"></i></div>
          <div class="icon-card-content">
            <h3>Door Codes</h3>
          </div>
        </div>
        <div class="card-body">
          <dl>
            <dt>Street Door</dt>
            <h4><span class="badge badge-primary">{{ $outerDoorCode }}</span></h4>
            <hr>
            <dt>Inner Door</dt>
            <h4><span class="badge badge-primary">{{ $innerDoorCode }}</span></h4>
          </dl>
        </div>
      </div>
    </div>

    <div class="col-sm">
      <div class="card">
        <div class="card-header icon-card-body">
          <div class="icon-card-icon"><i class="fas fa-wifi" aria-hidden="true"></i></div>
          <div class="icon-card-content">
            <h3>Member&apos;s WiFi</h3>
          </div>
        </div>
        <div class="card-body">
          <dl>
            <dt>SSID</dt>
            <h4><span class="badge badge-primary">{{ $wifiSsid }}</span></h4>
            <hr>
            <dt>Password (PSK)</dt>
            <h4><span class="badge badge-primary">{{ $wifiPass }}</span></h4>
          </dl>
        </div>
      </div>
    </div>

    <div class="col-sm">
      <div class="card">
        <div class="card-header icon-card-body">
          <div class="icon-card-icon"><i class="fas fa-wifi" aria-hidden="true"></i></div>
          <div class="icon-card-content">
            <h3>Guest WiFi</h3>
          </div>
        </div>
        <div class="card-body">
          <dl>
            <dt>SSID</dt>
            <h4><span class="badge badge-primary">{{ $guestWifiSsid }}</span></h4>
            <hr>
            <dt>Password (PSK)</dt>
            <h4><span class="badge badge-primary">{{ $guestWifiPass }}</span></h4>
          </dl>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
