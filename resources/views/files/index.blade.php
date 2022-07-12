@extends('layout')
@section('title', 'Browse')
@section('content')
@include('menu')
<div class="container">
    <div class="row mt-3">
        @if($errors->any())
        <div class="alert alert-danger" role="alert">
            {{$errors->first()}}
        </div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">File Name</th>
                    <th scope="col">Uploader</th>
                    <th scope="col">Options</th>
                </tr>
            </thead>
            <tbody>
            @foreach($filesArray as $file)
                <tr>
                    <th scope="row"><a class="streched-link text-decoration-none text-reset" target="_black" href="{{$file->url}}">{{$file->name}}</a></th>
                    <td>{{$file->uploader_name}}</td>
                    <td>
                        <form action="/files/delete" method="post">
                            @csrf
                            <input type="hidden" name="file_name" value="{{$file->location}}">
                            <button type="submit" class="btn {{ $file->this_user ? 'btn-danger' : 'btn-secondary'}}"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection