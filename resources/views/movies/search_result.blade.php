@extends("template")
@section("content")
    <h2><small class="text-muted">Results for</small> "{{ $query }}"</h2>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    @foreach($movies as $role => $movies)
                        <h3><mark>{{ $role }}</mark></h3>

                        <div class="table-responsive">
                            <table id="demo-dynamic-tables-1" class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Tagline</th>
                                    <th>Released</th>
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
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@endsection
