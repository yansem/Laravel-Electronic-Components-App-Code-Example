@extends('layouts.app')

@section('title', __('Electronic components'))

@section('content')
    <style>
        .overlay {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1000;
            top: 0;
            left: 0;
            opacity: 0.5;
            filter: alpha(opacity=50);
            background: #fff;
        }

        .overlay > div {
            height: 100%;
            align-items: center;
        }
    </style>
   <div id="app">
       <div class="overlay">
           <div class="d-flex justify-content-center">
               <div class="spinner-grow text-primary" role="status" style="width: 3rem; height: 3rem; z-index: 20;">
                   <span class="sr-only"></span>
               </div>
           </div>
       </div>
   </div>
@endsection
@section('local_storage')
    <script>
        localStorage.setItem('canEdit', {{ $canEdit }})
    </script>
@endsection
