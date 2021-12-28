<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a href="/" class="navbar-brand">MovieCatalog</a>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('movies.create') }}">Create movie</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('report1') }}">Report 1</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('report2') }}">Report 2</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('report3') }}">Report 3</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('report4') }}">Report 4</a>
                </li>
            </ul>
        </div>

        <form action="{{ route('search') }}" class="d-flex">
            <input value="{{ $query ?? '' }}" name="search" class="form-control me-2" type="search" placeholder="Search movies" aria-label="Search" autocomplete="off">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
    </div>
</nav>

