@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Newsletter Editor</h1>
        <div class="space-x-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline">← Back to Dashboard</a>
            <a href="{{ route('newsletter-editor.create') }}" class="btn btn-primary">Create New Newsletter</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-base-100 rounded-lg shadow p-6">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Title</th>
                        <th>Filename</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($steps as $step)
                        <tr>
                            <td>{{ $step->order }}</td>
                            <td>{{ $step->title }}</td>
                            <td><code>{{ $step->filename }}</code></td>
                            <td>
                                <span class="badge {{ $step->draft ? 'badge-warning' : 'badge-success' }}">
                                    {{ $step->draft ? 'Draft' : 'Published' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('newsletter-editor.edit', $step->id) }}" class="btn btn-sm btn-outline">Edit</a>
                                <a href="/preview/newsletter/{{ $step->order }}" target="_blank" class="btn btn-sm btn-ghost">Preview</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-500">No newsletters found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection