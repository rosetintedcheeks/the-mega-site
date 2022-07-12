@extends('layout')
@section('title', 'Requests')
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
                    <th scope="col">Request</th>
                    <th class="col-1" scope="col">Requester</th>
                    <th class="col-1 text-center" scope="col">Filled</th>
                    <th class="col-1 text-center" scope="col">Delete</th>
                </tr>
            </thead>
            <tbody>
            @foreach($bRequestsArray as $bRequest)
                <tr>
                    <th scope="row">{{$bRequest->name}}</th>
                    <td>{{$bRequest->requester_name ?? ''}}</td>
                    <td onClick="document.location.href='/request/fill/{{$bRequest->id}}';" class="text-center" style="cursor: pointer;">{{$bRequest->filled ? '✅' : '❌'}}</td>
                    <td onClick="document.location.href='/request/delete/{{$bRequest->id}}';" class="text-center"><i class="bi bi-trash"></i></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection