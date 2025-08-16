@extends('layouts.login')

@section('content')
    <div class="bg-overlay"></div>
    <div class="center-container" id="app">
        <div class="panel">
            <div class="bg"></div>
            <div class="bg-overlay"></div>
            <div class="content">
                <div class="form-group text-center">
                    <img src="{{ config('rcp.APP_URL') }}/img/jringrose300_.png" width="310" height="auto" alt="Logo">
                </div>
                <div class="form-group text-2xl font-semibold text-center">
                    <h2>CR Marketing</h2>
                    <login></login>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="w-full text-center mx-auto align-content-center justify-center text-gray-400">
                Copyright RCP Learning, Inc. 2011-2025
            </div>
        </div>
@endsection
