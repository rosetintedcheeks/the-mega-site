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
                    <th scope="col">Name</th>
                    <th scope="col">View</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 0; ?>
            @foreach($videosArray as $video)
                <?php $i += 1; ?>
                <tr>
                    <th scope="row"><a class="streched-link text-decoration-none text-reset" target="_black" href="{{$video->url}}">{{$video->title}}</a></th>
                    <td><i class="bi bi-eye" id="dbutton-<?= $i ?>"></i></td>
                    <td><div id="dropdown-<?= $i ?>" style="display:none;"><video controls src="{{$video->url}}" preload="none" width="640"></video></div></td>
                    <!-- <td>
                        <form action="/videos/delete" method="post">
                            @csrf
                            <input type="hidden" name="file_name" value="{--{$video->location}--}">
                            <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>-->
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="downloadajax"></div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script>
        /*$.ajax({
            url: '/videos/download',
            method: 'GET',
            success: function (res){
                $('downloadajax').html(res);
            },
        })*/
        $(document).ready(function() {
            for(let i = 0; i < <?= $i ?>; i++){
                $('#dbutton-' + i).click(function(){
                    $('#dropdown-' + i).css('display', function(i, v){
                        return v == 'none' ? 'block' : 'none'
                    });
                });
            }
        });
        </script>
    </div>
</div>
@endsection