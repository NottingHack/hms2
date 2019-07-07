@extends('layouts.app')

@section('pageTitle', 'Add new Product')

@section('content')
<div class="container">
  <p>To add a new Snackspace product, please fill in the short description, full description, and price. Make certain they're clear and easy to understand, especially in a list of transactions.</p>
</div>
<div class="container">
  <form role="form" method="POST" action="{{ route('snackspace.products.store') }}">
    @csrf

    <div class="form-group">
      <label for="shortDescription" class="form-label">Short Description</label>
      <input id="shortDescription" class="form-control" type="text" name="shortDescription" placeholder="Short Description" value="{{ old('shortDescription') }}" required autofocus>
      @if ($errors->has('shortDescription'))
      <p class="help-text">
        <strong>{{ $errors->first('shortDescription') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="longDescription" class="form-label">Description</label>
      <textarea id="longDescription" name="longDescription" class="form-control" placeholder="Description Here" rows="10">{{ old('longDescription') }}</textarea>
      @if ($errors->has('longDescription'))
      <p class="help-text">
        <strong>{{ $errors->first('longDescription') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="barcode" class="form-label">Barcode</label>
      <input id="barcode" class="form-control" type="text" name="barcode" placeholder="Optional Barcode" value="{{ old('barcode') }}">
      @if ($errors->has('barcode'))
      <p class="help-text">
        <strong>{{ $errors->first('barcode') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="price" class="form-label">Price in pence</label>
      <input id="price" class="form-control" type="number" name="price" placeholder="in pence" value="{{ old('price') }}" required>
      @if ($errors->has('price'))
      <p class="help-text">
        <strong>{{ $errors->first('price') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <button type="submit" class="btn btn-primary btn-block">Add Product</button>
    </div>
  </form>
</div>
</div>
@endsection
