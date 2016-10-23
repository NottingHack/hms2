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

                @can('view')
                <div class="panel-body">
                    You have the 'view' permission.
                </div>
                @endcan

                @can('view.other')
                <div class="panel-body">
                    You have the 'view other' permission.
                </div>
                @endcan

            </div>
        </div>
    </div>
</div>
@endsection
