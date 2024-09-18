@if (Session::has('errors'))
    <div class="alert alert-danger">
        <ul>
            @foreach (Session::get('errors')->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif