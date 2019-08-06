@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col">
      <h1 >Now viewing {{ $user->getFullname() }}</h1>
      <hr>
    </div>
  </div>
  <div class="card-columns">
    @component('admin.card.user', ['user' => $user, 'memberStatus' => $memberStatus])
    @endcomponent
    @component('admin.card.profile', ['user' => $user])
    @endcomponent
    @component('admin.card.banking', ['user' => $user, 'bankTransactions' => $bankTransactions])
    @endcomponent
    @component('admin.card.access', ['user' => $user])
    @endcomponent
    @component('admin.card.projects', ['user' => $user, 'projects' => $projects])
    @endcomponent
    @component('admin.card.teams', ['user' => $user, 'teams' => $teams])
    @endcomponent
    @component('admin.card.snackspace', ['user' => $user, 'snackspaceTransactions' => $snackspaceTransactions])
    @endcomponent
    @component('admin.card.tools', ['user' => $user, 'bookings' => $bookings, 'toolIds' => $toolIds, 'tools' => $tools])
    @endcomponent
    @component('admin.card.boxes', ['user' => $user, 'boxCount' => $boxCount])
    @endcomponent
    @component('admin.card.ban', ['user' => $user])
    @endcomponent
  </div>
</div>
@endsection
