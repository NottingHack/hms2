@extends('layouts.app')

@section('content')

<style>

</style>

<div class="container">
    <div class="row">

		<div class="container">
			<h1 class="display-4">Welcome, {{ Auth::user()->getFirstName() }}</h1>
	<hr>
        <!-- Example row of columns -->
        <div class="row">
          <div class="col-sm">
            <h2>Heading</h2>
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-secondary" href="#" role="button">View details »</a></p>
          </div>
          <div class="col-sm">
            <h2>Heading</h2>
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-secondary" href="#" role="button">View details »</a></p>
          </div>
          <div class="col-sm">
            <h2>Heading</h2>
            <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
            <p><a class="btn btn-secondary" href="#" role="button">View details »</a></p>
          </div>
        </div>

      </div>
            <div class="panel panel-default">

                <div class="panel-body">
                    You are logged in!
                </div>

                @can('profile.view.self')
                <div class="panel-body">
                    You have the 'profile.view.self' permission.
                </div>
                @endcan

                @can('profile.view.all')
                <div class="panel-body">
                    You have the 'profile.view.all' permission.
                </div>
                @endcan

            </div>
        </div>
    </div>
</div>
@endsection
