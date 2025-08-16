@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit Newsletter Sequence</h1>
        <a href="{{ route('newsletter-sequences.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
    
    <div id="app">
        <newsletter-sequence-edit :sequence-id="{{ $sequence->id }}"></newsletter-sequence-edit>
    </div>
</div>
@endsection