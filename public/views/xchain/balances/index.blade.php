@extends('platformAdmin::layouts.app')

@section('title_name') XChain Balances @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>XChain Balances</h1>
    </div>


    <table class="u-full-width">
      <thead>
        <tr>
          <th>Address</th>
          <th>ID</th>
          <th>Balances</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($xchain_balances as $balance_entry)
        <tr>
          <td>{{ $balance_entry['address'] }}</td>
          <td>{{ $balance_entry['id'] }}</td>
          <td><div style="max-width: 350px; overflow-x: scroll;"><code>{{ json_encode($balance_entry['balances'], 192) }}</code></div></td>
          <td>
            <a class="button button-primary" href="{{ route('platform.admin.xchain.balances.edit', ['id' => $balance_entry['id']]) }}">Edit</a>

            {{-- inline delete form --}}
            <form onsubmit="return confirm('Are you sure you want to delete this balance?')" action="{{ route('platform.admin.xchain.balances.destroy', ['id' => $balance_entry['id']]) }}" method="POST" style="margin-bottom: 0; display: inline;">
            <input type="hidden" name="_method" value="DELETE">
              <button type="submit" class="button-primary">Delete</button>
            </form>

          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

</div>

@endsection

