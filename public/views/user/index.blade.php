@extends('platformAdmin::layouts.app')

@section('title_name') Users @endsection

@section('body_content')

<div class="container" style="margin-top: 3%">
    <div class="row">
        <h1>Users</h1>
    </div>
    <p>
        <strong># of Users:</strong> {{ number_format(count($users)) }}
    </p>
    <div class="row">
      <table class="u-full-width">
        <thead>
          <tr>
            <th>Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Special Privileges</th>
            <th>Register Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($users as $user)
          <tr>
            <td>{{ $user['name'] }}</td>
            <td>{{ $user['username'] }}</td>
            <td>{{ $user['email'] }}</td>
            <td><code>{{ json_encode($user['privileges'], 192) }}</code></td>
            <td>{{ $user->created_at->format('F j\, Y \a\t g:i A') }}</td>
            <td>
              <a class="button button-primary" href="{{ route('platform.admin.user.edit', ['id' => $user['id']]) }}">Edit</a>

              {{-- inline delete form --}}
              <form onsubmit="return confirm('Are you sure you want to delete this user?')" action="{{ route('platform.admin.user.destroy', ['id' => $user['id']]) }}" method="POST" style="margin-bottom: 0; display: inline;">
              <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="button-primary">Delete</button>
              </form>

            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="row">
      <a class="button button-primary" href="{{ route('platform.admin.user.create') }}">Add a New User</a>
    </div>
</div>

@endsection

