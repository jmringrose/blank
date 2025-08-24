@extends('layouts.guest')

@section('content')
    <div class="flex">
        <div class="card w-96 shadow-sm align-content-center bg-stone-400 shadow-accent-content">
            <figure>
                <img
                    src="/img/emails/_DSC8767.jpg"
                    alt="Monkey"/>
            </figure>
            <div class="card-body">
                <h2 class="card-title">Invalid Unsubscribe Link</h2>
                <p>This unsubscribe link is invalid or has already been used.</p>
                <p>If you're still receiving unwanted emails, please contact us directly.</p>
                <div class="card-actions justify-end">
                    <a role="button" class="btn btn-primary" href="https://www.realcoolphototours.com">See Tours</a>
                </div>
            </div>
        </div>
    </div>
@endsection