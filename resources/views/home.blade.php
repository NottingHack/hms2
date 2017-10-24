@extends('layouts.app')

@section('content')

<style>

</style>

<div class="container">
    <div class="row">

		<div class="container">
			<h1 class="display-4">Welcome, {{ Auth::user()->getFirstName() }}</h1>
	<hr>
        </div>
    </div>
</div>
            
<div class="container">
  <div class="row">
      
      <div class="card-deck">
      
        <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
                <div class="card-header icon-card-body">
        <div class="icon-card-icon"><i class="fa fa-key" aria-hidden="true"></i></div>
        <div class="icon-card-content">
            <h3>Hackspace is</h3>
        </div>
          </div>
            <div class="card-body">
            <h1 class="card-text text-center">Open</h1>
            </div>
            <div class="card-footer"></div>
        </div>
      
      
        <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
                <div class="card-header icon-card-body">
        <div class="icon-card-icon"><i class="fa fa-newspaper-o" aria-hidden="true"></i></div>
        <div class="icon-card-content">
            <h3>Notice</h3>
        </div>
          </div>
            <div class="card-body">
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
            <div class="card-footer"></div>
        </div>
      
          
      

        <div class="card text-white bg-info mb-3" style="max-width: 20rem;">
                <div class="card-header icon-card-body">
        <div class="icon-card-icon"><i class="fa fa-money" aria-hidden="true"></i></div>
        <div class="icon-card-content">
            <h3>Balance</h3>
        </div>
          </div>
            <div class="card-body">
            <h1 class="card-text text-center">£0.00</h1>
            </div>
            <div class="card-footer"></div>
        </div>


        <div class="card text-white bg-secondary mb-3" style="max-width: 20rem;">
                <div class="card-header icon-card-body">
        <div class="icon-card-icon"><i class="fa fa-wrench" aria-hidden="true"></i></div>
        <div class="icon-card-content">
            <h3>Active Projects</h3>
        </div>
          </div>
            <div class="card-body">
            <h1 class="card-text text-center">0</h1>
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
      
      
    </div>
  </div>
@endsection
