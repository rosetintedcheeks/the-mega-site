@extends('layout')
@section('title', 'Upload')
@section('content')
@include('menu')
<div class="container">
    <div class="row mt-3">
    <form action="{{route('sounds.upload')}}" method="post" class="" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="formFile" class="form-label">Upload your sound here</label>
            <input class="form-control mb-3" type="file" id="formFile" name="file">
            <label for="command_name" class="form-label">What command will be used to play this sound?</label>
            <div class="input-group mb-3">
                <label for="command_name" class="input-group-text">!</label><input class="form-control" type="text" name="command_name">
            </div>
            <button type="submit" class="btn btn-success mt-3">Upload</submit>
        </div>
    </form>
    </div>
</div>
@endsection