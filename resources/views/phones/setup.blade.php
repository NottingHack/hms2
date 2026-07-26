@extends('layouts.app')

@section('pageTitle', 'Extension Setup')

@section('content')
<div class="container">
  @if ($extension->getType() === 'SIP')
  <h4>Configuration Details</h4>

  <table class="table table-bordered table-hover">
    <tbody>
      <tr>
        <th class="w-20">SIP Server</th>
        <td>{{ $sip_server }}</td>
      </tr>
      <tr>
        <th>User ID</th>
        <td>{{ $extension->getExtension() }}</td>
      </tr>
      <tr>
        <th>Password</th>
        <td>{{ $extension->getSipPassword() }}</td>
      </tr>
    </tbody>
  </table>
  @endif
  @if ($extension->getMappedNumber())
  <h3>You're good to go!</h3>
  <p>This number has already been mapped to a handset / line</p>
  @else
  @if ($extension->getType() === 'DECT')
  <h3>Step 1: Register your handset</h3>
  <p>From the options on your handset, register the handset using the pin <strong>{{ $dect_registration_pin }}</strong>.</p>
  @endif
  @if ($extension->getType() === 'POTS')
  <h3>Step 1: Plugin your phone</h3>
  <p>You may need to speak to the Network Team for help with this.</p>
  @endif
  @if ($extension->getType() === 'POTS' || $extension->getType() === 'DECT')
  <h3>Step 2: Link to your extension</h3>
  <p>Dial <strong>{{ $link_prefix }}{{ str_pad((string)$extension->getExtension(), 5, '0', STR_PAD_LEFT) }}</strong> and wait for the message confirming that the handset has been registered to your extension.</p>

  <h3>Step 3: Wait a bit</h3>
  <p>It can take some time for the configuration on the phone system to be reloaded</p>
  @endif
  @endif
</div>
@endsection
