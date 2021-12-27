@extends("neo4j.template")
@section("content")

<form name="form" action="/neo4j/rating" method="post">
@if(isset($nodeProp))
<div>
<table>
  <tr>
    <th>ID</th><td>{{$nodeProp["id"]}}</td>
  </tr>
  <tr>
    <th>タイトル</th><td>{{$nodeProp["title"]}}</td>
  </tr>
  <tr>
    <th>リリース年</th><td>{{$nodeProp["released"]}}</td>
  </tr>
  <tr>
    <th>タグ</th><td>{{$nodeProp["tagline"]}}</td>
  </tr>
  <tr>
    <td>評価</td>
    <td>
      <select name="rating">
      <option value="0" @if($nodeProp["rating"] == 0) selected @endif >-</option>
      <option value="1" @if($nodeProp["rating"] == 1) selected @endif >★</option>
      <option value="2" @if($nodeProp["rating"] == 2) selected @endif >★★</option>
      <option value="3" @if($nodeProp["rating"] == 3) selected @endif >★★★</option>
      <option value="4" @if($nodeProp["rating"] == 4) selected @endif >★★★★</option>
      <option value="5" @if($nodeProp["rating"] == 5) selected @endif >★★★★★</option>
      </select>
    </td>
  </tr>
</table>
</div>
<div>
  <input type="submit" value="評価">
</div>
<input type="hidden" name="movie_id" value="{{$nodeProp['id']}}">
@endif
<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
</form>

@endsection