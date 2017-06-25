@extends('layouts.app')

@section('pageTitle', 'Access Codes')

@section('content')

<div class="row">
    <div class="card small-12 medium-6 columns">
        <div class="card-section">
            <h4>
                <i class="fa fa-key" aria-hidden="true"></i> Door Codes
            </h4>
            <dl>
                <dt>Street Door</dt>
                <dd>{{ $outerDoorCode }}</dd>
                <dt>Inner Door</dt>
                <dd>{{ $innerDoorCode }}</dd>
            </dl>
        </div>
    </div>

    <div class="card small-12 medium-6 columns">
        <div class="card-section">
            <h4>
                <i class="fa fa-wifi" aria-hidden="true"></i> Members&apos; WiFi
            </h4>
            <dl>
                <dt>SSID</dt>
                <dd>{{ $wifiSsid }}</dd>
                <dt>PSK</dt>
                <dd>{{ $wifiPass }}</dd>
            </dl>
        </div>
    </div>

    <div class="card small-12 medium-6 columns">
        <div class="card-section">
            <h4>
                <i class="fa fa-wifi" aria-hidden="true"></i> Guest WiFi
            </h4>
            <dl>
                <dt>SSID</dt>
                <dd>{{ $guestWifiSsid }}</dd>
                <dt>PSK</dt>
                <dd>{{ $guestWifiPass }}</dd>
            </dl>
        </div>
    </div>
</div>

@endsection