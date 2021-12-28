@extends("template")
@section("content")
    <h2>Editing person</h2>
    <form method="POST" action="{{ route('people.update', $person['id']) }}">
        @method('PUT')
        @csrf
        <div class="form-group">
            <label for="exampleInputEmail1">Name</label>
            <input type="text" name="name" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                   placeholder="Enter name"
                   value="{{ $person['name'] }}"
            >
        </div>
        <div class="form-group">
            <label for="exampleInputEmail">Born</label>
            <input type="number" name="born" class="form-control" id="exampleInputEmail" aria-describedby="emailHelp"
                   placeholder="Enter birth date"
                   value="{{ $person['born'] }}"
            >
        </div>
        <button class="btn btn-danger mt-4" type="submit">Update</button>
    </form>
    <h2><small class="text-muted">Movies around</small> {{ $person['name'] }}</h2>

    @foreach($person['movies'] as $role => $movies)
            <h2>
                <mark>{{ $role }}</mark>
            </h2>
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
@endsection
