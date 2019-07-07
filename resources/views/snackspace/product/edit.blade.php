@extends('layouts.app')

@section('pageTitle')
Editing {{ $product->getShortDescription() }}
@endsection

@section('content')
<div class="container">
  <form role="form" method="POST" action="{{ route('snackspace.products.update', $product->getId()) }}">
    @csrf
    @method('PATCH')

    <div class="form-group">
      <label for="shortDescription" class="form-label">Short Description</label>
      <input id="shortDescription" class="form-control" type="text" name="shortDescription" placeholder="Short Description" value="{{ old('shortDescription', $product->getShortDescription()) }}" required autofocus>
      @if ($errors->has('shortDescription'))
      <p class="help-text">
        <strong>{{ $errors->first('shortDescription') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="longDescription" class="form-label">Description</label>
      <textarea id="longDescription" name="longDescription" class="form-control" placeholder="Description Here" rows="10">{{ old('longDescription', $product->getLongDescription()) }}</textarea>
      @if ($errors->has('longDescription'))
      <p class="help-text">
        <strong>{{ $errors->first('longDescription') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="barcode" class="form-label">Barcode</label>
      <input id="barcode" class="form-control" type="text" name="barcode" value="{{ old('barcode', $product->getBarcode()) }}">
      @if ($errors->has('barcode'))
      <p class="help-text">
        <strong>{{ $errors->first('barcode') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="price" class="form-label">Price in pence</label>
      <input id="price" class="form-control" type="number" name="price" placeholder="in pence" value="{{ old('price', $product->getPrice()) }}" required>
      @if ($errors->has('price'))
      <p class="help-text">
        <strong>{{ $errors->first('price') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <button type="submit" class="btn btn-primary btn-block">Update Prdduct</button>
    </div>
  </form>
</div>
@endsection

