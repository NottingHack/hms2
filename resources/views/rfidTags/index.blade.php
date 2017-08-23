@extends('layouts.app')

@section('pageTitle')
Rfid cards for {{ $user->getFirstname() }}
@endsection

@section('content')
<table>
    <thead>
        <tr>
            <th>Card Serial Number</th>
            <th>Last Used</th>
            <th>Name</th>
            <th>State</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach ($rfidTags as $rfidTag)
        <tr>
            <td>{{ $rfidTag->getBestRfidSerial() }}</td>
            <td>{{ $rfidTag->getLastUsed() ? $rfidTag->getLastUsed()->toDateTimeString() : '' }}</td>
            <td>{{ $rfidTag->getFriendlyName() }}</td>
            <td>{{ $rfidTag->getStateString() }}</td>
            <td>
            @can('rfidTags.edit.self')
            <a href="{{ route('rfid_tags.edit', $rfidTag->getId()) }}"><i class="fa fa-edit fa-lg" aria-hidden="true"></i> Edit</a>
            @endcan
            @can('rfidTags.destroy')
              <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="alert">
                <form action="{{ route('rfid_tags.destroy', $rfidTag->getId()) }}" method="POST" style="display: none">
                  {{ method_field('DELETE') }}
                  {{ csrf_field() }}
                </form>
                <i class="fa fa-trash fa-lg" aria-hidden="true"></i> Remove
              </a>
            @endcan

            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<div classs="pagination-links">
    {{ $rfidTags->links() }}
</div>
@endsection
