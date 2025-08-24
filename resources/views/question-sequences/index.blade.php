@extends('layouts.app')

@section('content')
    <div class="container mt-4 max-w-5xl mx-auto text-base bg-base-300 p-1 md:p-3 rounded-lg shadow-lg border">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Question Recipients</h1>
            <button id="addUserBtn" class="btn btn-primary">
                <span class="material-symbols-outlined">person_add</span>
                Add User
            </button>
        </div>
        <div id="app">
            <question-datatable></question-datatable>
        </div>
    </div>
@endsection