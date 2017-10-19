    <div class="u-pull-right">
        {!! Form::model(Illuminate\Support\Facades\Input::all(), ['method' => 'get', 'route' => $pagination['route_prefix'].'.index']) !!}
        <label for="name" style="display: inline;">Filter By </label>
        {!! Form::text('name', null, ['placeholder' => 'Name']) !!}
        {!! Form::text('username', null, ['placeholder' => 'Username']) !!}
        {!! Form::text('email', null, ['placeholder' => 'Email']) !!}
        <button type="submit" class="button-primary">Search</button>
        {!! Form::close() !!}

    </div>
    <div class="row">&nbsp;</div>
