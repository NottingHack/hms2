@extends('layouts.app')

@section('pageTitle', 'Print Label')

@section('content')
<div class="container">
  <div class="card">
    <h4 class="card-header">{{ $templateName}}</h4>
    <div class="card-body">
      <pre class="card-text">{{ $template}}</pre>
    </div>
    <div class="card-footer">
      <form role="form" method="POST" action="{{ route('labels.print', $templateName) }}">
        {{ csrf_field() }}
        @if ( ! empty($fields))
        <p>This template requires the following information to print.</p>
        @foreach ($fields as $field)
        <div class="form-group">
          <label for="{{ $field }}" class="form-label">{{ $field }}</label>
          <input class="form-control" id="{{ $field }}" type="text" name="{{ $field }}" value="{{ old($field, '') }}" required autofocus>
          @if ($errors->has($field))
          <p class="help-text">
            <strong>{{ $errors->first($field) }}</strong>
          </p>
          @endif
        </div>
        @endforeach
        @endif
        @if (SiteVisitor::inTheSpace())
        <p>How many copies would you like.</p>
        <hr>
        <div class="form-group">
          <label for="copiesToPrint" class="form-label">Copies</label>
          <input class="form-control" style="width: 5%" id="copiesToPrint" type="text" name="copiesToPrint" value="{{ old('copiesToPrint', '1') }}" required>
          @if ($errors->has('copiesToPrint'))
          <p class="help-text">
            <strong>{{ $errors->first('copiesToPrint') }}</strong>
          </p>
          @endif
        </div>
        <hr>
        <div class="card">
          <button type="submit" class="btn btn-success"><i class="fas fa-print" aria-hidden="true"></i> Print</button>
        </div>
        @else
        <div class="row">
          <p>Labels can only be printed from within the space.</p>
        </div>
        @endif
      </form>
    </div>
  </div>
</div>
@endsection
