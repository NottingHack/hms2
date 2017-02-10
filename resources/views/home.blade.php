@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

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
