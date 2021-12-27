<nav class="navbar navbar-light bg-light mb-3">
    <div class="container-fluid">
        <a href="/" class="navbar-brand">MovieCatalog</a>
        <form action="{{ route('search') }}" class="d-flex">
            <input value="{{ $query ?? '' }}" name="search" class="form-control me-2" type="search" placeholder="Search" aria-label="Search" autocomplete="off">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
    </div>
</nav>

