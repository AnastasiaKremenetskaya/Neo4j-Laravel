@extends("template")
@section("content")
    <h2>Creating movie</h2>
    <form method="POST" action="{{ route('movies.store') }}">
        @csrf
        <div class="form-group mb-3">
            <label for="exampleInputEmail1">Title</label>
            <input required type="text" name="title" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                   placeholder="Enter title"
            >
        </div>
        <div class="form-group mb-3">
            <label for="exampleInputEmail">Released</label>
            <input required type="number" name="released" class="form-control" id="exampleInputEmail"
                   aria-describedby="emailHelp"
                   placeholder="Enter release date"
            >
        </div>
        <div class="form-group mb-3">
            <label for="exampleInputEmail1">Tagline</label>
            <input type="text" name="tagline" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                   placeholder="Enter tagline"
            >
        </div>

        <h2><small class="text-muted mt-4">People around</small> creating film</h2>
        <h4>
            <mark>Acted in</mark>
        </h4>
        <div class="form-group mb-3">
            <label for="exampleFormControlSelect2">Choose actors</label>
            <select multiple class="form-control" id="exampleFormControlSelect2" name="relations[ACTED_IN][]">
                @foreach($people as $key => $person)
                    <option value="{{ $key }}">{{ $person }}</option>
                @endforeach
            </select>
        </div>

        <h4>
            <mark>Directed</mark>
        </h4>
        <div class="form-group mb-3">
            <label for="exampleFormControlSelect2">Choose directors</label>
            <select multiple class="form-control" id="exampleFormControlSelect2" name="relations[DIRECTED][]">
                @foreach($people as $key => $person)
                    <option value="{{ $key }}">{{ $person }}</option>
                @endforeach
            </select>
        </div>

        <h4>
            <mark>Produced</mark>
        </h4>
        <div class="form-group mb-3">
            <label for="exampleFormControlSelect2">Choose producers</label>
            <select multiple class="form-control" id="exampleFormControlSelect2" name="relations[PRODUCED][]">
                @foreach($people as $key => $person)
                    <option value="{{ $key }}">{{ $person }}</option>
                @endforeach
            </select>
        </div>

{{--        <h4>--}}
{{--            <mark><input>Produced</mark>--}}
{{--        </h4>--}}
{{--        <div class="form-group mb-3">--}}
{{--            <label for="exampleFormControlSelect2">Choose producers</label>--}}
{{--            <select multiple class="form-control" id="exampleFormControlSelect2" name="relations[PRODUCED][]">--}}
{{--                @foreach($people as $person)--}}
{{--                    <option {{ $person }}>{{ $person }}</option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}

        <button class="btn btn-danger mt-4" type="submit">Create</button>
    </form>

@endsection
