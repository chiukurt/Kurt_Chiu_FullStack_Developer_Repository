@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Drop Pins</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ url('/import') }}"
                              method="post"
                              enctype="multipart/form-data">

                            @if (session('errors'))
                                @foreach ($errors as $error)
                                    <li>{{$error}}</li>
                                @endforeach
                            @endif
                            @if (session('success'))
                                {{session('success')}}
                            @endif

                            {{csrf_field()}}
                            <br>

                            Please select an excel file to upload

                            <br>
                            <input type="file" name="file" id="file">
                            <br>
                            <button type="submit"> Upload File</button>
                        </form>
                        <br>

                        <h1>List of files:</h1>
                        <ul>
                            @foreach ($file_list as $file)
                                <li>
                                    {{$file}}
                                </li>
                            @endforeach

                        </ul>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
