@extends('layouts.guest')

@section('content')
    <div class="flex">
        <div class="card w-96 shadow-sm align-content-center bg-stone-400 shadow-accent-content">
            <figure>
                <img
                    src="/img/emails/_DSC8767.jpg"
                    alt="Shoes"/>
            </figure>
            <div class="card-body">
                <h2 class="card-title">Confirmed, You're Unsubscribed</h2>
                <p>You've been successfully unsubscribed from our newsletter. We've taken you off the mailing list.</p>
                <div class="card-actions justify-end">
                    <a role="button" class="btn btn-primary" href="https://www.realcoolphototours.com">See Tours</a>
                </div>
            </div>
        </div>


    </div>
@endsection