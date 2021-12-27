@extends("template")
@section("content")

<form name="form" action="/neo4j/search" method="post">
Release year:<input type="text" name="released" size="10" maxlength="9" value=@if(isset($formReleased)) "{{$formReleased}}" @endif>
<input type="submit" value="SEARCH">

@if(isset($movies) && count($movies) > 0)
<div>
  <table>
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Release year</th>
    </tr>
    @foreach($movies as $movie)
    <tr>
      <td><a href="/detail?movie_id={{$movie['id']}}">{{$movie["id"]}}</a></td>
      <td><a href="/recommend?movie_id={{$movie['id']}}">{{$movie["title"]}}</a></td>
      <td>{{$movie["released"]}}</td>
    </tr>
    @endforeach
  </table>
</div>
@endif
<input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>
@endsection
