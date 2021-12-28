@extends("template")
@section("content")
    <div class="d-flex justify-content-between">
        <h2><small class="text-muted">Results for</small> "{{ $query }}"</h2>
        @if(!empty($showEditPersonButton))
            <button class="btn btn-outline-primary flex-end justify-content-end"><a class="text-decoration-none" href="{{ route('people.edit', $query) }}">Edit person</a></button>
        @endif
    </div>
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
