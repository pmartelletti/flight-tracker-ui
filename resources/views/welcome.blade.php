@extends('layouts.app')

@section('content')
<div class="container" id="app" xmlns="http://www.w3.org/1999/html">
    <template v-if="!routes">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Find your flight</div>

                <div class="panel-body">
                  <form class="form-horizontal" role="form" method="GET" action="{{ url('/api/classic-form') }}">

                      <div class="row">
                        {{-- right column  --}}
                        <div class="col-md-6">
                          {{-- departure city --}}
                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="fromCity" class="col-md-4 control-label">From</label>

                              <div class="col-md-8">
                                <select name="fromCity" class="form-control">
                                  @foreach($cities as $iso => $city)
                                    <option value="{{ $iso }}">{{ $city }}</option>
                                  @endforeach
                                </select>
                                <p class="help-block">Where does your journey starts.</p>
                              </div>
                          </div>

                          {{-- departure day  --}}
                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="departureDay" class="col-md-4 control-label">What day of the week would you like to travel? </label>

                              <div class="col-md-8">
                                <select name="departureDay" class="form-control">
                                  @foreach($days as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                  @endforeach
                                </select>
                              </div>
                          </div>

                          {{-- departure day - not this week --}}
                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="startDate" class="col-md-4 control-label">Start Day? </label>

                              <div class="col-md-8">
                                <input type="date" name="startDate" class="form-control" value="{{ date('Y-m-d') }}">
                                <p class="help-block">Leave empty if you want to start looking from this week.</p>
                              </div>
                          </div>

                        </div>

                        {{-- left column --}}
                        <div class="col-md-6">
                          {{-- arrival city --}}
                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="toCity" class="col-md-4 control-label">To</label>

                              <div class="col-md-8">
                                <select name="toCity" class="form-control">
                                <option value="anywhere" selected="selected">Anywhere</option>
                                  @foreach($cities as $iso => $city)
                                    <option value="{{ $iso }}">{{ $city }}</option>
                                  @endforeach
                                </select>
                                <p class="help-block">Leave empty to search in all available cities.</p>
                              </div>
                          </div>

                          {{-- how many days  --}}
                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="tripDays" class="col-md-4 control-label">How many nights? </label>

                              <div class="col-md-8">
                                <input type="number" name="tripDays" class="form-control" placeholder="i.e: 5" value="5">
                              </div>
                          </div>

                            {{-- weeks to look for --}}
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="howFar" class="col-md-4 control-label"># Weeks Flexibility</label>

                                <div class="col-md-8">
                                    <input type="number" name="howFar" class="form-control" placeholder="i.e: 10" value="1">
                                    <p class="help-block">Starting from departure date, how many weeks in the future flights can be. More flexibility can help to find cheaper prices.</p>
                                </div>
                            </div>

                          {{-- max leg duration  --}}
                          {{--<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">--}}
                              {{--<label for="legDuration" class="col-md-4 control-label">Max Leg duration (hs.)? </label>--}}

                              {{--<div class="col-md-8">--}}
                                {{--<input type="text" name="legDuration" class="form-control" placeholder="i.e: 3">--}}
                                {{--<p class="help-block">This is the max time you'd like to take to arrive at destination (including stops).</p>--}}
                              {{--</div>--}}
                          {{--</div>--}}

                        </div>
                      </div>

                      {{-- <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                          <label for="name" class="col-md-4 control-label">Name</label>

                          <div class="col-md-6">
                              <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">

                              @if ($errors->has('name'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('name') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div> --}}
                      <div class="form-group">
                          <div class="col-md-2 col-md-offset-10">
                              <button type="submit" @click.prevent="submitForm()" class="btn btn-primary" :disabled="processing">
                                  <i class="fa fa-btn fa-plane" :class="{'fa-spin': processing}"></i> Find Flights
                              </button>
                          </div>
                      </div>
                  </form>

                </div>
                <div class="panel-footer">
                    <p class="text-right">
                        <a href="http://www.skyscanner.net" class="logo">Powered by <img src="http://business.skyscanner.net/Content/images/logo/ssf-white-color.png" width="211" height="47" alt="Skyscanner"></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    </template>

    <template v-if="routes">
        @include('results')
    </template>


</div>
@endsection


@section('scripts')
<script type="text/javascript">
    new Vue({
      el: '#app',
      data: {
          processing: false,
          routes: null,
          carriers: null,
          airports: null,
          priceRange: null,
          filters: {
              priceRange: {
                  min: 0,
                  max: 1000
              },
              carrier: '',
              query: ''
          },
          searchName: ''
      },
      methods: {
          submitForm: function () {
              this.processing = true
              var url = $('form').attr('action') + '?' + $('form').serialize()
              this.$http.get(url).then(response => {
                var data = response.data
                this.searchName = data.name
                this.routes = data.routes
                this.carriers = data.carriers
//                this.airports = []
                this.processing = false
                this.priceRange = {min: data.priceRange.min, max: data.priceRange.max }
                this.filters.priceRange = data.priceRange
              }).catch(error => {
                  alert('Error! Please try again later.')
                  this.processing = false
              })
          },
          resetSearch: function() {
            this.routes = null
            this.carriers = null
            this.airports = null
          },
          resetFilters() {
                this.filters.priceRange = this.priceRange
                this.filters.carrier = ''
          },
          priceRangeFilter: function(value) {
            var price = value.$value.price
            return this.filters.priceRange.min <= price && this.filters.priceRange.max >= price
          }
      }
    });
//  $(document).ready(function(){
//    $('form').submit(function(e){
//      $(this).find('button[type="submit"]').addClass('disabled');
//      $(this).find('.fa-plane').addClass('fa-spin');
//    })
//  })
</script>
@endsection
