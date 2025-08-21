@extends('layouts.app')

@section('content')

    <div class="container mt-4 max-w-3xl mx-auto text-base bg-base-300 p-1 md:p-3 rounded-lg shadow-lg border">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold ml-4">Edit Newsletter Sequence</h1>
            <a href="{{ route('newsletter-sequences.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
        <div id="app" class="mb-6">
            <newsletter-sequence-edit :sequence-id="{{ $sequence->id }}"></newsletter-sequence-edit>
        </div>
    </div>


    </div>
@endsection
