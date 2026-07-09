<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accept Invitation</title>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
</head>
<body>
    <h1>Accept Invitation</h1>

    <p>
        Hi {{ $invitation->name }},
    </p>

    <p>
        You were invited to join {{ $invitation->company->name }} as {{ ucfirst($invitation->role) }} using {{ $invitation->email }}.
    </p>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
        @csrf

        <p>
            <label for="password">Password</label><br>
            <input id="password" type="password" name="password" required autocomplete="new-password">
        </p>

        <p>
            <label for="password_confirmation">Confirm Password</label><br>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        </p>

        <button type="submit">Accept Invitation</button>
    </form>
</body>
</html>
