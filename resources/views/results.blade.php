<div class="row">
    <div class="col-md-12">
        <p class="text-right">
            <a class="btn btn-info" href="{{ url('/') }}" @click.prevent="resetSearch">Start new search</a>
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
{{--            <div class="panel-heading">{{ $routes }}</div>--}}
            <div class="panel-heading">@{{ searchName }}</div>
            <div class="panel-body">

                <form class="form-inline">
                    <div class="form-group">
                        <div class="input-group">
                            <select v-model="filters.carrier" class="form-control">
                                <option value="">All Carriers</option>
                                <option v-for="carrier in carriers | orderBy 'carrier' " :value="carrier">
                                    @{{ carrier }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <select v-model="filters.destination" class="form-control">
                                <option value="">All Destinations</option>
                                <option v-for="destination in destinations | orderBy 'destination'" :value="destination">
                                    @{{ destination }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="sr-only" for="exampleInputAmount">Amount (in dollars)</label>
                        <div class="input-group">
                            <div class="input-group-addon">From</div>
                            <input type="text" class="form-control" v-model="filters.priceRange.min" placeholder="Amount">
                            <div class="input-group-addon">EUR</div>
                        </div>
                        <div class="input-group">
                            <div class="input-group-addon">To</div>
                            <input type="text" class="form-control" v-model="filters.priceRange.max" placeholder="Amount">
                        <div class="input-group-addon">EUR</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary pull-right" @click.prevent="resetFilters()"><i class="fa fa-times"></i> Clear filters</button>
                </form>

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
                    <template v-for="route in routes | orderBy 'price' | filterBy filters.destination | filterBy filters.carrier | filterBy priceRangeFilter ">
                        <tr>
                            <td>@{{ route.outbound.carrier }}</td>
                            <td>@{{ route.outbound.origin }}</td>
                            <td>@{{ route.outbound.destination }}</td>
                            <td>@{{ route.outbound.departureDate }}</td>
                            <td>@{{ route.direct ? 'Yes' : 'No' }}</td>
                            <td rowspan="2" class="text-center info">
                                From: <br/>
                                <span style="font-size: 20px">@{{ route.price | currency '€' }}</span></br>
                                Fare found @{{ route.daysQuoted }} <br>
                                <a class="btn btn-success" href="@{{ route.referralUrl }}" target="_blank">Find flights</a>
                            </td>
                        </tr>
                        <tr>
                            <td>@{{ route.inbound.carrier }}</td>
                            <td>@{{ route.inbound.origin }}</td>
                            <td>@{{ route.inbound.destination }}</td>
                            <td>@{{ route.inbound.departureDate }}</td>
                            <td>@{{ route.direct ? 'Yes' : 'No' }}</td>
                        </tr>
                        <tr>
                            <td colspan="6"></td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>