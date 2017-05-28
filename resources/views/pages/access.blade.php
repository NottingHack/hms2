@extends('layouts.app')

@section('pageTitle', 'Access Codes')

@section('content')
<h2>Access Codes</h2>

<p>The Hackspace door codes and WiFi passwords</p>
<div>
<dl>
    <dt>
        Street Door Code
    </dt>
    <dd>
        {{ $outerDoorCode }}
    </dd>
    <dt>
        Inner Door Code
    </dt>
    <dd>
        {{ $innerDoorCode }}
    </dd>
    <dt>
        WiFi SSID
    </dt>
    <dd>
        {{ $wifiSsid }}
    </dd>
    <dt>
        WiFi PSK
    </dt>
    <dd>
        {{ $wifiPass }}
    </dd>
    <dt>
        Guest WiFi SSID
    </dt>
    <dd>
        {{ $guestWifiSsid }}
    </dd>
    <dt>
        Guest WiFi PSK
    </dt>
    <dd>
        {{ $guestWifiPass }}
    </dd>
</dl>
</div>
@endsection
