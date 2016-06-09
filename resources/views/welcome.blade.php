@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Find your flight</div>

                <div class="panel-body">
                  <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                      {{ csrf_field() }}

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
                                <input type="date" name="startDate" class="form-control" placeholder="i.e: 3">
                                <p class="help-block">Leave empty if you want to start looking from this week.</p>
                              </div>
                          </div>

                          {{-- weeks to look for --}}
                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="howFar" class="col-md-4 control-label"># Weeks Flexibility</label>

                              <div class="col-md-8">
                                <input type="text" name="howFar" class="form-control" placeholder="i.e: 10">
                                <p class="help-block">Starting from departure date, how many weeks in the future flights can be. More flexibility can help to find cheaper prices.</p>
                              </div>
                          </div>

                        </div>

                        {{-- left column --}}
                        <div class="col-md-6">
                          {{-- arrival city --}}
                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="toCity" class="col-md-4 control-label">To</label>

                              <div class="col-md-8">
                                <select name="toCity" class="form-control" multiple="multiple">
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
                                <input type="text" name="tripDays" class="form-control" placeholder="i.e: 5">
                              </div>
                          </div>

                          {{-- max leg duration  --}}
                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="legDuration" class="col-md-4 control-label">Max Leg duration (hs.)? </label>

                              <div class="col-md-8">
                                <input type="text" name="legDuration" class="form-control" placeholder="i.e: 3">
                                <p class="help-block">This is the max time you'd like to take to arrive at destination (including stops).</p>
                              </div>
                          </div>

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
                              <button type="submit" class="btn btn-primary">
                                  <i class="fa fa-btn fa-plane"></i> Find Flights
                              </button>
                          </div>
                      </div>
                  </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
  <script type="text/javascript">
  $(document).ready(function(){
    $('form').submit(function(e){
      $(this).find('button[type="submit"]').addClass('disabled');
      $(this).find('.fa-plane').addClass('fa-spin');
    })
  })
  </script>
@endsection
