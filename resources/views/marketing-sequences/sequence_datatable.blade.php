@extends('layouts.app')
@section('content')

     <div class="container mt-4 max-w-[1200px] mx-auto text-base bg-base-300 p-1 md:p-3 rounded-lg shadow-lg border">
         <div class="flex justify-between items-center mb-6">
             <h1 class="text-2xl font-bold ml-4">Sales Prospects</h1>
             <div class="flex items-center ">
                 <a role="button" class="btn btn-primary mr-2" href="marketing-steps">
                     <span class="material-symbols-outlined">edit</span>
                     Marketing Emails
                 </a>
             </div>
         </div>
         <div id="app">
             <datatable></datatable>
         </div>
     </div>

@endsection
