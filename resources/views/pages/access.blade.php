@extends('layouts.app')

@section('pageTitle', 'Access Codes')

@section('content')


<div class="container">
  <div class="row">
    <div class="col-sm">
      <div class="card my-3">
    <div class="icon-card-body">
        <div class="icon-card-icon"><i class="fa fa-key" aria-hidden="true"></i></div>
        <div class="icon-card-content">
            <h3>Door Codes</h3>
            <dl>
                <dt>Street Door</dt>
                <dd>{{ $outerDoorCode }}</dd>
                <dt>Inner Door</dt>
                <dd>{{ $innerDoorCode }}</dd>
            </dl>
        </div>
    </div>
</div>
    </div>
    <div class="col-sm">
     <div class="card my-3">
    <div class="icon-card-body">
        <div class="icon-card-icon">
            <i class="fa fa-wifi" aria-hidden="true"></i>
        </div>
        <div class="icon-card-content">
            <h3>Member&apos;s WiFi</h3>
            <dl>
                <dt>SSID</dt>
                <dd>{{ $wifiSsid }}</dd>
                <dt>PSK</dt>
                <dd>{{ $wifiPass }}</dd>
            </dl>
        </div>
    </div>
</div>
    </div>
    <div class="col-sm">
      <div class="card my-3">
    <div class="icon-card-body">
        <div class="icon-card-icon">
            <i class="fa fa-wifi" aria-hidden="true"></i>
        </div>
        <div class="icon-card-content">
            <h3>Guest WiFi</h3>
            <dl>
                <dt>SSID</dt>
                <dd>{{ $guestWifiSsid }}</dd>
                <dt>PSK</dt>
                <dd>{{ $guestWifiPass }}</dd>
            </dl>
        </div>
    </div>
</div>
    </div>
  </div>
</div>





@endsection