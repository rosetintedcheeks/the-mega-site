@extends('layout')
@section('title', 'Browse Join Sounds')
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
                    <th scope="col">File Name</th>
                    <th scope="col">Use this sound?</th>
                    <th scope="col">Options</th>
                    <th scope="col">View File</th>
                </tr>
            </thead>
            <tbody>
            @foreach($joinSoundsArray as $sound)
                @if($sound->this_user)
                <tr>
                    <td>
                        {{$sound->name}}
                    </td>
                    <td onClick="document.location.href='/joinsounds/check/{{$sound->id}}';" class="text-center" style="cursor: pointer;">{{$sound->checked ? '✅' : '❌'}}</td>
                    <td>
                        <form action="/joinsounds/delete" method="post">
                            @csrf
                            <input type="hidden" name="sound_name" value="{{$sound->location}}">
                            <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                    <td><a class="btn btn-primary text-decoration-none text-reset" target="_black" href="{{$sound->url}}"><i class="bi bi-eye" style="color:white;"></i></a></td>
                </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection