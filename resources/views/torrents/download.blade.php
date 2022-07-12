@extends('layout')
@section('title', 'Browse')
@section('content')
@include('menu')
<div class="container">
    <form method="POST" id="torrent-search" action="/torrents/search">
        @csrf
        <input type="text" name="name">
        <button type="submit">Submit</button>
    </form>
    <div id="result" class="row"></div>
    <style>
        .card-img-top::after {
            content: "";
            width: 100%;
            background: rgba(255, 255, 255, .3);
            display: block;
        }
    </style>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script>
// this is the id of the form
$("#torrent-search").submit(function(e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var actionUrl = form.attr('action');
    
    $.ajax({
        type: "POST",
        url: actionUrl,
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
            const dataObject = JSON.parse(data);
            

            var resultHTML = '';
            dataObject.data.forEach(function(listItem){
                const name = listItem.attributes.name;
                const poster = listItem.attributes.poster;
                const seeders = listItem.attributes.seeders;
                const linkId = listItem.attributes.link_id;
                resultHTML += '<div class="col mb-1">'
                    + '<div class="card" style="width: 18rem;">'
                        + '<img class="card-img-top" style="width:92px; margin: auto;" src="' + poster + '">'
                        + '<div class="card-body">'
                            + '<h5 class="card-title">' + name + '</h5>'
                            + '<p class="card-text">'
                            + 'Seeders: ' + seeders 
                            + '</p>'
                        + '<a href="/torrents/download?link_id=' + linkId +'" class="btn btn-primary">Go somewhere</a>'
                        + '</div>'
                    + '</div>'
                + '</div>';
            });
            $('#result').html(resultHTML);
        }
    });
    
});
</script>
@endsection
