@extends('platformAdmin::layouts.app')

@section('title_name') Token Promises @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>Token Promises</h1>
    </div>

    <table class="u-full-width">
      <thead>
        <tr>
          <th>Destination</th>
          <th>Quantity</th>
          <th>Asset</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($promises as $promise)
        <tr>
          <td>{{ $promise['destination'] }}</td>
          <td>{{ Tokenly\CurrencyLib\CurrencyUtil::satoshisToValue($promise['quantity']) }}</td>
          <td>{{ $promise['asset'] }}</td>
          <td>
            <a class="button button-primary" href="{{ route('platform.admin.promise.edit', ['id' => $promise['id']]) }}">Edit</a>

            {{-- inline delete form --}}
            <form onsubmit="return confirm('Are you sure you want to delete this balance?')" action="{{ route('platform.admin.promise.destroy', ['id' => $promise['id']]) }}" method="POST" style="margin-bottom: 0; display: inline;">
            <input type="hidden" name="_method" value="DELETE">
              <button type="submit" class="button-primary">Delete</button>
            </form>

          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="row">
      <a href="{{ route('platform.admin.promise.create') }}" class="button button-primary">Create a New Promise</a>
    </div>
</div>


@endsection

