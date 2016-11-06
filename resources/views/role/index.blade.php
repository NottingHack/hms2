@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Roles</div>

                @foreach ($roles as $category => $categoryRoles)
                    <h2>{{ $category }}</h2>
                    @foreach ($categoryRoles as $role)
                    <p>{{ $role['name'] }}</p>
                    @endforeach
                @endforeach


                </div>
        </div>
    </div>
</div>
@endsection
