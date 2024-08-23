@extends('layouts.app')

@section('pageTitle')
Register Of Members
@endsection

@section('content')
<div class="container">
  {{-- @can('governance.registerOfMembers.view.grant')
  <div class="row mb-2">
    <div class="col">
      Give temporary view permission to ...
    </div>
  </div>
  @endcan --}}
  <div class="row">
    <div class="col">
      <div class="pagination-links">
        {{ $registerOfMembers->links() }}
      </div>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th class="w-30" scope="col">Name</th>
            <th class="w-25" scope="col">Service Address</th>
            <th class="w-5" scope="col">Membership Guarantee</th>
            <th class="w-20" scope="col">Date Membership Started</th>
            <th class="w-20" scope="col">Date Membership Ended</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($registerOfMembers as $record)
          <tr>
            <td>{{ $record->getFullname() }}</td>
            <td>
              {{ config('branding.company_name') }}<br>
              {{ config('branding.space_address_1') }}<br>
              {{ config('branding.space_address_2') }}<br>

              @if (config('branding.space_address_3'))
              {{ config('branding.space_address_3') }}<br>
              @endif

              {{ config('branding.space_city') }}<br>

              @if (config('branding.space_county'))
              {{ config('branding.space_county') }}<br>
              @endif

              {{ config('branding.space_postcode') }}<br>
            </td>
            <td>£1</td>
            <td>{{ $record->getStartedAt()->toDateString() }}</td>
            <td>{{ $record->getEndedAt()?->toDateString() }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="pagination-links">
        {{ $registerOfMembers->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
