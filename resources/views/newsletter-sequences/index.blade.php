@extends('layouts.app')

@section('content')
    <div class="container mt-4 max-w-5xl mx-auto text-base bg-base-300 p-1 md:p-3 rounded-lg shadow-lg border">
        <div class="flex justify-between items-right mb-6">
            <h1 class="text-2xl font-bold ml-2">Newsletter Recipients</h1>
            <div class="flex items-center ">
                <a role="button" class="btn btn-primary mr-2" href="newsletter-steps">
                    <span class="material-symbols-outlined">edit</span>
                    Newsletters
                </a>
                <button id="addUserBtn" class="btn btn-primary">
                    <span class="material-symbols-outlined">person_add</span>
                    Add User
                </button>
            </div>
        </div>
        <div id="app">
            <newsletter-datatable></newsletter-datatable>
        </div>
    </div>
@endsection
