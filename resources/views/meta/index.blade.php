@extends('layouts.app')

@section('pageTitle', 'Meta Values')

@section('content')
<table>
    <thead>
        <tr>
            <th>Key</th>
            <th>Value</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($metas as $meta)
        <tr>
            <td>{{ $meta->getKey() }}</td>
            <td>{{ $meta->getValue() }}</td>
            <td>
            @can('meta.edit')
                <a href="{{ route('metas.edit', $meta->getKey()) }}">Edit</a>
            @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<div classs="pagination-links">
    {{ $metas->links() }}
</div>
@endsection
