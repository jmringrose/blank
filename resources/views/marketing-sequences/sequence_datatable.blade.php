@extends('layouts.app')
@section('content')

     <div class="container mt-4 max-w-5xl mx-auto text-base bg-base-300 p-1 md:p-3 rounded-lg shadow-lg border">
         <div class="flex justify-between items-center mb-6">
             <h1 class="text-2xl font-bold">Sales Prospects</h1>
         </div>
         <div id="app">
             <datatable></datatable>
         </div>
     </div>

@endsection
