@extends('layouts.layout')
@section('content')

    <header id="header">
        <div class="header-wrap">
            <h1 class="header-text">Get the position of your website!</h1>
            <h3 class="header-text">Example with Netherlands</h3>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="row">
                <form action="{{route('save')}}" class="form-group" method="post">
                    <div class="form-group">
                        <label for="search-eng">Search engine:</label>
                        <select class="form-control" id="search-eng" name="searchEng">
                            @foreach($searchEngines as $id=>$name)
                                <option value="{{$name}}">{{$name}}</option>
                            @endforeach
                        </select>
                        <label for="location">Location:</label>
                        <select class="form-control" id="location" name="location">
                            @foreach($locations as $location)
                                <option value="{{$location['loc_id']}}">{{$location['loc_name']}}</option>
                            @endforeach
                        </select>
                        <label for="website">Website:</label>
                        <input type="text" class="form-control" id="website" name="website" value="{{old('website')}}">
                        @if ($errors->has('website'))
                            <span class="help-block">
                            <strong>{{ $errors->first('website') }}</strong>
                        </span>
                        @endif
                        <label for="keywords">Keywords:</label>
                        <textarea class="form-control" rows="5" id="keywords" name="keywords"
                                  placeholder="Each words starts with new line">{{old('keywords')}}</textarea>
                        @if ($errors->has('keywords'))
                            <span class="help-block">
                            <strong>{{ $errors->first('keywords') }}</strong>
                        </span>
                        @endif
                        <br>
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                    {{csrf_field()}}
                </form>
                <a class=" btn btn-default" href="{{route('result')}}">To result page</a>
            </div>
        </div>
    </main>
@endsection
