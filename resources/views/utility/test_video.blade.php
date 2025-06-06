@extends('layouts.app')
@section('content')
    <div class="container max-w-5xl mx-auto text-base">
        <h1 class="mb-3 bg-base-200 mb-2 mt-6 w-24 rounded ">Test Video</h1>
        <videoplayer video-url="bunny.mp4" showtitle=""></videoplayer>
        <status-button></status-button>
        <status-panel></status-panel>
    </div>
@endsection
