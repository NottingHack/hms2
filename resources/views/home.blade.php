@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col">
      <h1 class="display-4">Welcome, {{ $user->getFirstName() }}</h1>
      <hr>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <div class="card-deck">
        <div class="card text-white bg-info">
          <div class="card-header icon-card-body">
            <div class="icon-card-icon"><i class="fas fa-key" aria-hidden="true"></i>
            </div>
            <div class="icon-card-content">
              <h3>Hackspace</h3>
            </div>
          </div>
          <div class="card-body">
            <h1 class="card-text text-center">{status}</h1>
          </div>
          <div class="card-footer">
            <small class="text-white">Last updated 3 mins ago</small>
          </div>
        </div>

        <div class="card text-white bg-warning">
          <div class="card-header icon-card-body">
            <div class="icon-card-icon"><i class="fal fa-newspaper" aria-hidden="true"></i>
            </div>
            <div class="icon-card-content">
              <h3>Notice</h3>
            </div>
          </div>
          <div class="card-body">
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
          </div>
          <div class="card-footer">
            <small class="text-white">Last updated 3 mins ago</small>
          </div>
        </div>

        @if($user->getProfile() !== Null)
        <div class="card text-white bg-dark">
          <div class="card-header icon-card-body">
            <div class="icon-card-icon"><i class="far fa-money-bill" aria-hidden="true"></i>
            </div>
            <div class="icon-card-content">
              <h3>Balance</h3>
            </div>
          </div>
          <div class="card-body">
            <h1 class="card-text text-center"><strong>@format_pennies($user->getProfile()->getBalance())</strong></h1></h1>
          </div>
          <div class="card-footer">
            <small class="text-white">Last updated 3 mins ago</small>
          </div>
        </div>
        @endif

        <div class="card text-white bg-secondary">
          <div class="card-header icon-card-body">
            <div class="icon-card-icon"><i class="fas fa-wrench" aria-hidden="true"></i>
            </div>
            <div class="icon-card-content">
              <h3>Active Projects</h3>
            </div>
          </div>
          <div class="card-body">
            <h1 class="card-text text-center">{{ $projectCount }}</h1>
          </div>
          <div class="card-footer">
            <small class="text-white">Last updated 3 mins ago</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <br>
      <h2>Blah Blach</h2>
      <small>By That guy</small>
      <hr>
      <p>Lorem ipsum dolor sit amet, autem sanctus an mel. Alii doming nec ut. Nonumes deseruisse quo ne, ea vim ignota malorum perpetua. Has diam recusabo temporibus in, inimicus laboramus at vel, discere nominavi oportere vis et. Ne eum omnesque constituto, ei tale sumo blandit nec. Pro cu velit oporteat constituam. Ferri possim an mea, an est cibo honestatis. Mei nominavi recteque eu, vide nominati rationibus an his. Vix ludus congue ut, usu ullum integre partiendo ex. Melius necessitatibus eu nec, nostrud pericula voluptatibus eos in. Nonumy facilisis no has, eos erat feugait scribentur ut. Sensibus intellegebat in sit. Eum an facer scripta vulputate, eos at novum senserit. Odio etiam explicari usu ex, ad volutpat hendrerit mea. Est ex veritus sadipscing. Sea veri euismod cu. Ad molestiae voluptatum instructior mel, quando aliquando pri ea. Sea cu laudem appareat, id accumsan apeirian adolescens sit. Ei aeterno fabellas ocurreret his. Ius odio cibo constituto et. Vix tale magna ea, aliquid scriptorem ne per, te elit reprehendunt eam. Vis nulla dicit feugiat eu, cibo deleniti evertitur ei pro. No sed dignissim dissentiunt reprehendunt. Cum fabulas expetendis ut, esse definiebas concludaturque duo in.</p>
    </div>
  </div>

</div>
@endsection
