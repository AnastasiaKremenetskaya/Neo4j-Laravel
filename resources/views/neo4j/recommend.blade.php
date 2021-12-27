@extends("neo4j.template")

@section("content")
@if(isset($recommends) && count($recommends) > 0)
<div>
<table>
<tr>
  <th>名前</th>
  <th>タイトル</th>
</tr>
@foreach($recommends as $recommend)
<tr>
  <td>{{$recommend["name"]}}</td>
  <td><a href="/neo4j/recommend?movie_id={{$recommend['id']}}">{{$recommend["title"]}}</a></td>
</tr>
@endforeach
</table>
</div>
@endif
@endsection