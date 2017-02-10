@extends('layouts.app')

@section('content')
<h1>Meta</h1>
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
            <td><a href="{{ route('metas.edit', $meta->getKey()) }}">Edit</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
<div classs="pagination-links">
    {{ $metas->links() }}
</div>
@endsection
