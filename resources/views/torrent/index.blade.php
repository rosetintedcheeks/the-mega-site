@extends('layout')
@section('title', 'Browse')
@section('content')
@include('menu')
<div class="container">
    <form method="POST" id="torrent-search" action="/torrent/search">
        @csrf
        <input type="text" name="name" placeholder="Search">
        <br>
        <br>
        <label for="imdb">IMDB id:&nbsp;</label><input type="text" name="imdb">
        <br>
        <br>
        <button type="submit">Submit</button>
        <br>
        <br>
    </form>
    <div id="status-button"><button type="button" class="btn btn-primary otherDownloadBtn">Download</button><!-- doesn't work <a class="btn btn-secondary" target="_blank" href="https://rosetintedcheeks.com/rt">Torrent status</a>--></div>
    <div id="result" class="row"></div>
    <style>
        .card-img-top::after {
            content: "";
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, .3);
            display: block;
        }
        #torrent-search {
            display: inline-block;
        }
        #status-button {
            width:200px;
            float: right;
            margin-bottom: 20px;
        }
        #result {
            width: 100%;
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
                        //+ '<img class="card-img-top" style="width:92px; margin: auto;" src="' + poster + '">'
                        + '<div class="card-body">'
                            + '<h5 class="card-title">' + name + '</h5>'
                            + '<p class="card-text">'
                            + 'Seeders: ' + seeders
                            + '</p>'
                        + '<button type="button" data-link-id="' + linkId +'" class="btn btn-primary downloadBtn">Download</button>'
                        + '</div>'
                    + '</div>'
                + '</div>';
            });
            $('#result').html(resultHTML);
            $('.downloadBtn').click(function() {
                $('#downloadModal').modal('show');
                $('#linkField').val($(this).data('link-id'));
                $('#downloadModelForm').attr("action", "/torrent/download");
                $('#anime-option').css("display", "none");
                $('#file-upload').css("display", "none");
            });
        }
    });

});

$(document).ready(function() {
    $('#downloadModal').modal();
    $('button.close').click(function() {
        $('#downloadModal').modal('hide');
    });
    $('.otherDownloadBtn').click(function() {
        $('#downloadModal').modal('show');
    });
});

</script>

<!-- Modal -->
<div class="modal fade" id="downloadModal" tabindex="-1" role="dialog" aria-labelledby="downloadModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form method="POST" id="downloadModelForm" action="/torrent/upload" enctype="multipart/form-data">
        @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="downloadModalLabel">Download Torrent</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <input id="linkField" type="hidden" name="link_id" value="">
            <div class="form-check" id="anime-option">
                <input class="form-check-input" type="radio" name="media_type" id="typeAnime" value="anime" required>
                <label class="form-check-label" for="typeAnime">
                    Anime
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="media_type" id="typeTV" value="TV" required>
                <label class="form-check-label" for="typeTV">
                    TV
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="media_type" id="typeMovie" value="movie" required>
                <label class="form-check-label" for="typeMovie">
                    Movie
                </label>
            </div>

            <br>
            <div class="form-group">
                <label for="media_name">Media Name</label>
                <input type="text" class="form-control" id="mediaNameInput" aria-describedby="mediaName" name="media_name">
                <small id="emailHelp" class="form-text text-muted">Name of the show/movie</small>
            </div>
            <br>
            <div class="form-group">
                <label for="media_name">Season</label>
                <input type="text" class="form-control" id="mediaNameInput" aria-describedby="mediaName" name="season">
            </div>
            <div class="form-group" id="file-upload">
                <label for="torrentFile">File</label>
                <input type="file" name="torrentFile" id="torrentFile">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="closeBtn" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" id="submitBtn" class="btn btn-primary">Save changes</button>
      </div>
    </form>
    </div>
  </div>
</div>
@endsection
