@extends('layouts.app')

@section('pageTitle', 'Product')

@section('content')
<div class="container">
  <h1>{{ $product->getShortDescription() }}</h1>
  <hr>
  <div class="table-responsive">
    <table class="table table-bordered table-hover ">
      <tbody>
        <tr>
          <th>Short Description:</th>
          <td>{{ $product->getShortDescription() }}</td>
        </tr>
        <tr>
          <th>Description:</th>
          <td>{{ $product->getLongDescription() }}</td>
        </tr>
        <tr>
          <th>Barcode:</th>
          <td>{{ $product->getBarcode() }}</td>
        </tr>
        <tr>
          <th>Price:</th>
          <td><span class="money">@money($product->getPrice(), 'GBP')</span></td>
        </tr>
      </tbody>
    </table>
  </div>
  @can('snackspace.product.edit')
  <a href="{{ route('snackspace.products.edit', $product->getId()) }}" class="btn btn-info btn-block"> <i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>

  @endcan
</div>
@endsection
