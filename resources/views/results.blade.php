@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <p class="text-right">
                <a class="btn btn-info" href="{{ url('/') }}">Start new search</a>
            </p>
        </div>
    </div>
    <div class="row">
        {{--<div class="col-md-2">--}}
            {{--<div class="panel panel-default">--}}
                {{--<div class="panel-heading">Carriers</div>--}}
                {{--<div class="panel-body">--}}
                    {{--<ul>--}}
                        {{--@foreach($routes->getCarriers() as $carrier)--}}
                        {{--<li>{{ $carrier }}</li>--}}
                        {{--@endforeach--}}
                    {{--</ul>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">{{ $routes }}</div>
                <div class="panel-body">

                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th>Carrier</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Date</th>
                            <th>Direct</th>
                            <th class="text-center">Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($routes->getRoutes()->sortBy('price') as $route)
                        <tr>
                            <td>{{ $route['outbound']['carrier'] }}</td>
                            <td>{{ $route['outbound']['origin'] }}</td>
                            <td>{{ $route['outbound']['destination'] }}</td>
                            <td>{{ $route['outbound']['departureDate'] }}</td>
                            <td>{{ $route['direct'] ? 'Yes' : 'No' }}</td>
                            <td rowspan="2" class="text-center info">
                                From: <br/>
                                <span style="font-size: 20px">{{ $route['price'] }} EUR</span></br>
                                Fare found {{ $route['daysQuoted'] }} <br>
                                {{--<a class="btn btn-success">Find flights</a>--}}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ $route['inbound']['carrier'] }}</td>
                            <td>{{ $route['inbound']['origin'] }}</td>
                            <td>{{ $route['inbound']['destination'] }}</td>
                            <td>{{ $route['inbound']['departureDate'] }}</td>
                            <td>{{ $route['direct'] ? 'Yes' : 'No' }}</td>
                        </tr>
                        <tr>
                            <td colspan="6"></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection