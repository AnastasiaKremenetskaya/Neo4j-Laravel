@extends("template")
@section("content")
    <h2><small class="text-muted">People around</small> {{ $movie }}</h2>
    <div class="col-xs-12">
        <div class="panel">
            <div class="panel-body">
                @foreach($people as $role => $personInfo)
                    <h3>
                        <mark>{{ $role }}</mark>
                    </h3>
                    <div class="table-responsive container">
                        <table id="demo-dynamic-tables-1" class="table table-hover">
                            <thead>
                            <tr class="row">
                                <th class="col-2">Name</th>
                                <th class="col-2">Birth date</th>
                                <th class="col">Other movies</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($personInfo as $person => $info)
                                <tr class="row">
                                    <td class="col-2">
                                        <a href="{{ route('people.show', $person) }}">{{ $person }}</a>
                                    </td>
                                    <td class="col-2">{{ $info['born'] }}</td>
                                    <td colspan="4" class="col">
                                        @foreach($info['movies'] as $role => $movies)
                                            <h5>
                                                <mark>{{ $role }}</mark>
                                            </h5>
                                            <table class="table mb-0">
                                                <thead>
                                                <tr class="row">
                                                    <th class="col">Title</th>
                                                    <th class="col-6">Tagline</th>
                                                    <th class="col">Released</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($movies as $movie)
                                                    <tr class="row">
                                                        <td class="col">
                                                            <a href="{{ route('movies.show', $movie['title']) }}">{{ $movie['title'] }}</a>
                                                        </td>
                                                        <td class="col-6">
                                                            <i>{{$movie['tagline']}}</i>
                                                        </td>
                                                        <td class="col">
                                                            {{ $movie['released'] }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @endforeach
                                    </td>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
