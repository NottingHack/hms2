@extends('layouts.app')

@section('pageTitle', 'Print Label')

@section('content')
<h1>{{ $templateName}}</h1>
<!-- TODO: shade this area -->
<div>
    <pre>{{ $template }}</pre>
    <p/>
</div>

<div>
    <form role="form" method="POST" action="{{ route('labels.print', $templateName) }}">
        {{ csrf_field() }}
        @if ( ! empty($fields))
        <p>This template requires the following information to print.</p>
        @foreach ($fields as $field)
        <div class="row">
          <label for="{{ $field }}" class="form-label">{{ $field }}</label>
          <div class="form-control">
            <input id="{{ $field }}" type="text" name="{{ $field }}" value="{{ old($field, '') }}" required autofocus>
            @if ($errors->has($field))
            <p class="help-text">
              <strong>{{ $errors->first($field) }}</strong>
            </p>
            @endif
          </div>
        </div>
        @endforeach
        @endif

        @if (SiteVisitor::inTheSpace())
        <p>How many copies would you like.</p>
        <div class="row">
          <label for="copiesToPrint" class="form-label">Copies</label>
          <div class="form-control">
            <input id="copiesToPrint" type="text" name="copiesToPrint" value="{{ old('copiesToPrint', '1') }}" required autofocus>
            @if ($errors->has('copiesToPrint'))
            <p class="help-text">
              <strong>{{ $errors->first('copiesToPrint') }}</strong>
            </p>
            @endif
          </div>
        </div>
        <div class="row">
          <div class="form-buttons">
            <button type="submit" class="button">Print</button>
          </div>
        </div>
        @else
        <div class="row">
            <p>Labels can only be printed from within the space.</p>
        </div>
        @endif
    </form>
</div>
@endsection
