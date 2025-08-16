@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Newsletter Sequences</h1>
    </div>
    
    <div id="app">
        <newsletter-datatable></newsletter-datatable>
    </div>
</div>
@endsection