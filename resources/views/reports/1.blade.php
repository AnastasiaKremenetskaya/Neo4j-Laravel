@extends("template")
@section("content")
    <h2><small class="text-muted">Report 1</small></h2>
    <div class="col-xs-12">
        <div class="panel">
            <div class="panel-body">
                <h3>
                    <mark>Wrote</mark>
                </h3>
                <div class="table-responsive container">
                    <table id="demo-dynamic-tables-1" class="table table-hover">
                        <thead>
                        <tr class="row">
                            <th class="col-2">Name</th>
                            <th class="col-2">Birth date</th>
                            <th class="col">Wrote for</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($people as $person => $info)
                            <tr class="row">
                                <td class="col-2">
                                    <a href="{{ route('people.show', $person) }}">{{ $person }}</a>
                                </td>
                                <td class="col-2">{{ $info['born'] }}</td>
                                <td colspan="4" class="col">
                                    @foreach($info['movies'] as $movie)
                                        <table class="table mb-3">
                                            <thead>
                                            <tr class="row">
                                                <th class="col">Title</th>
                                                <th class="col-6">Tagline</th>
                                                <th class="col">Released</th>
                                            </tr>
                                            </thead>
                                            <tbody>
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
                                            </tbody>
                                        </table>
                                    @endforeach
                                </td>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
