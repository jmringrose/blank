@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Unsubscribe</h1>
        <p class="text-gray-600 mb-4">You have been unsubscribed from our mailing list.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Return to Dashboard</a>
    </div>
</div>
@endsection