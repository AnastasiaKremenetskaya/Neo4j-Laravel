@extends("template")
@section("content")
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="demo-dynamic-tables-1" class="table table-hover">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>Tagline</th>
                                <th>Released</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($movies as $movie)
                                <tr>
                                    <td>
                                        <a href="{{ route('movies.show', $movie['title']) }}">{{ $movie['title'] }}</a>
                                    </td>
                                    <td>
                                        {{ $movie['tagline'] }}
                                    </td>
                                    <td>
                                        {{ $movie['released'] }}
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('movies.delete', $movie['title']) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
