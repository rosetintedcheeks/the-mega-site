<form action="/videos/download" method="post">
    @csrf
    <input type="text" placeholder="URL" name="url">
    <button type="submit">Download</button>
</form>