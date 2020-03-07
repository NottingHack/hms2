@extends('layouts.app')

@section('pageTitle', 'Product')

@section('content')
<div class="container">
  <p>You can add a new Snackspace product by clicking on the Add New Product button. Or you can edit the existing products below.</p>
  <a href="{{ route('snackspace.products.create') }}"  class="btn btn-primary btn-block"><i class="fas fa-plus" aria-hidden="true"></i> Add new product</a>
  <hr>
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th>Price</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($products as $product)
        <tr>
          <td data-title="Name">{{ $product->getShortDescription() }}</td>
          <td data-title="Price"><span class="money">@money($product->getPrice(), 'GBP')</span></td>
          <td calss='actions'>
            <a class="btn btn-primary" href="{{ route('snackspace.products.show', $product->getId()) }}"><i class="fas fa-eye" aria-hidden="true"></i> View</a>
            @can('snackspace.vendingMachine.edit')
            <a class="btn btn-primary" href="{{ route('snackspace.products.edit', $product->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div classs="pagination-links">
    {{ $products->links() }}
  </div>
</div>
@endsection
