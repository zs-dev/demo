@extends('layouts.default')
@section('content')
<form method="POST" action="/crawl">
    @csrf
    <div class="row">
        <div class="col-sm-3">
            <label for="path">Path (empty will default to root):</label>
        </div>
        <div class="col-sm-8">
            <input type="text" id="path" name="path" value="{{ old('path') }}">
        </div>
        <div class="col-sm-3">
            <label for="number_of_pages">Number of pages:</label>
        </div>
        <div class="col-sm-8">
            <select name="number_of_pages" id="number_of_pages">
                <option value="4">4</option>
                <option value="5" @if (old('number_of_pages') == '5') selected="selected" @endif>5</option>
                <option value="6" @if (old('number_of_pages') == '6') selected="selected" @endif>6</option>
            </select><br/><br/>
        </div>
        <div class="col-sm-3">&nbsp;</div>
        <div class="col-sm-8">
                <input type="submit" value="Submit">

        </div>
@error('path')
   <span class="text-danger">{{$message}}</span>
@enderror
    </div>
</form>
@stop