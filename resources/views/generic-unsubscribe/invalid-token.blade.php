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
                <h2 class="card-title">Sorry, We Can't Find You</h2>
                <p>We're unable to process your unsubscribe request. This link may have expired or already been used.</p>
                <p>If you're still receiving emails and want to unsubscribe, please contact us directly.</p>
                <div class="card-actions justify-end">
                    <a role="button" class="btn btn-primary" href="https://www.realcoolphototours.com/contact">Contact Us</a>
                    <a role="button" class="btn btn-outline" href="https://www.realcoolphototours.com">See Tours</a>
                </div>
            </div>
        </div>
    </div>
@endsection