@extends('layout')
@section('title', 'Commands')
@section('content')
@include('menu')
<div class="container">
    <div class="row mt-3">
        @if($errors->any())
        <div class="alert alert-danger" role="alert">
            {{$errors->first()}}
        </div>
        @endif
        <table class="table text-center">
            <thead>
                <tr>
                    <th scope="col">Command Name</th>
                    <th scope="col">Uploader</th>
                    <th scope="col">Options</th>
                    <th scope="col">View File</th>
                </tr>
            </thead>
            <tbody>
            @foreach($soundsArray as $sound)
                <tr>
                    <td>
                        <form action="/sounds/editCommandName" method="post" class="row">
                            @csrf
                            <input type="hidden" name="sound_name" value="{{$sound->location}}">
                            <div class="input-group col">
                                <label for="command_name" class="input-group-text">!</label><input class="form-control" type="text" name="command_name" value="{{$sound->command_name}}" {{$sound->this_user ? '' : 'readonly'}}>
                            </div>
                            <button type="submit" class="col-1 btn {{ $sound->this_user ? 'btn-primary' : 'btn-secondary'}}"><i class="bi bi-pencil-square"></i></button>
                        </form>
                    </td>
                    <td style="vertical-align:middle;">{{$sound->uploader_name ?? 'Anonymous'}}</td>
                    <td>
                        <form action="/sounds/delete" method="post">
                            @csrf
                            <input type="hidden" name="sound_name" value="{{$sound->location}}">
                            <button type="submit" class="btn {{ $sound->this_user ? 'btn-danger' : 'btn-secondary'}}"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                    <td><a class="btn btn-primary streched-link text-decoration-none text-reset" target="_black" href="{{$sound->url}}"><i class="bi bi-eye"></i></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection