@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col">
      <h1 >Welcome, {{ $user->getFirstName() }}</h1>
      <hr>
    </div>
  </div>
  <div class="card-columns">{{--
    @component('card.user', ['user' => $user])
    @endcomponent
    @component('card.profile', ['user' => $user])
    @endcomponent --}}
    @component('card.projects', ['user' => $user])
    @endcomponent
    @component('card.teams', ['user' => $user])
    @endcomponent
    @component('card.snackspace', ['user' => $user])
    @endcomponent
    @component('card.tools', ['user' => $user])
    @endcomponent
    @component('card.boxes', ['user' => $user])
    @endcomponent
    @component('card.commonTasks', ['user_id' => $user->getId()])
    @endcomponent
    @component('card.access', ['user' => $user])
    @endcomponent
  </div>

</div>
@endsection
