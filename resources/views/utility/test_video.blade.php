@extends('layouts.app')
@section('content')
    <div class="container max-w-5xl mx-auto text-neutral-content p-2">
        <div class="mt-4 w-full rounded text-2xl px-4">Test Video & Pina</div>
        <div class="container mt-4 max-w-5xl mx-auto text-base bg-base-300 p-2 md:p-4 rounded-lg shadow-lg border border-zinc-400">
            <videoplayer video-url="bunny.mp4" showtitle=""></videoplayer>
            <status-button></status-button>
            <status-panel></status-panel>
            <status-light></status-light>

            <StoreDump store="status"></StoreDump>
        </div>
    </div>
@endsection
