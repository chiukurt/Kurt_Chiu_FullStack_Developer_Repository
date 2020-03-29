@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Lab Locations</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Privacy</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>Country</th>
                                <th>Google Maps</th>
                            </tr>
                            </thead>
                            <tbody>
                            @for ($i = 1 ; $i < sizeof($pins); $i++)
                                <tr>
                                    <td>{{$pins[$i]['title']}}</td>
                                    <td>{{$pins[$i]['category']}}</td>
                                    <td>{{$pins[$i]['privacy']}}</td>
                                    <td>{{$pins[$i]['address']}}</td>
                                    <td>{{$pins[$i]['city']}}</td>
                                    <td>{{$pins[$i]['country']}}</td>
                                    <td>
                                        <a href="{{ 'https://maps.google.com?q='
                                            .$pins[$i]['latitude'].','.$pins[$i]['longitude']}}"
                                           target="_blank">View Google Maps</a>
                                    </td>
                                </tr>
                            @endfor
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
