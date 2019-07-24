@extends('layouts.app')

@section('pageTitle', 'Electric Usage')

@section('content')
<div class="container">
  <div class=row>
    {!! $readingsChart->container() !!}
  </div>
</div>
@can('instrumentation.electric.addReading')
<div class="container">
  <p>
    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#addMeterReading" aria-expanded="false" aria-controls="addMeterReading">
      Add a meter reading
    </button>
  </p>
  <div class="collapse" id="addMeterReading">
    <div class="card card-body">
      <form role="form" method="POST" action="{{ route('instrumentation.electric.readings.store') }}">
        @csrf
        <div class="form-group">
        <label for="meter" class="form-label">Meter</label>
        <select-two
          :invalid="{{ $errors->has('meter') ? 'true' : 'false' }}"
          id='meter'
          name='meter'
          placeholder="Select a meter..."
          :options="{{ json_encode($meterOptions) }}"
          style="width: 100%"
          {{ old('meter') ? ':value="' . old('meter') . '"' : '' }}
          >
        </select-two>

        @if ($errors->has('meter'))
        <p class="invalid-feedback">
          <strong>{{ $errors->first('meter') }}</strong>
        </p>
        @endif
      </div>

      <div class="form-group">
        <label for="reading" class="form-label">Reading Value</label>
        <input class="form-control{{  $errors->has('reading') ? ' is-invalid' : '' }}" id="reading" type="number" name="reading" value="{{ old('reading') }}" required>
        @if ($errors->has('reading'))
        <p class="invalid-feedback">
          <strong>{{ $errors->first('reading') }}</strong>
        </p>
        @endif
      </div>

      <div class="form-group">
        <label for="date" class="form-label">Date</label>
        <input class="form-control{{  $errors->has('date') ? ' is-invalid' : '' }}" id="date" type="date" name="date" value="{{ old('date', $readingDate) }}" required>
        @if ($errors->has('date'))
        <p class="invalid-feedback">
          <strong>{{ $errors->first('date') }}</strong>
        </p>
        @endif
      </div>

      <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Add Reading</button>
      </form>
    </div>
  </div>
</div>
@endcan
@endsection

@push('scripts')
  {!! $readingsChart->script() !!}
@endpush

