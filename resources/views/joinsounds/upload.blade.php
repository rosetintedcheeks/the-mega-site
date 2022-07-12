@extends('layout')
@section('title', 'Upload')
@section('content')
@include('menu')
<div class="container">
    <div class="row mt-3">
    <form action="{{route('joinsounds.upload')}}" method="post" class="" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="formFile" class="form-label">Upload your sound here</label>
            <input class="form-control mb-3" type="file" id="formFile" name="file">
            <button type="submit" class="btn btn-success mt-3">Upload</submit>
        </div>
    </form>
    </div>
</div>
@endsection