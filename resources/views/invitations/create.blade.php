<x-app-layout>
    <x-slot name="header">
        <h1>Invite User</h1>
    </x-slot>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('invitations.store') }}">
        @csrf

        @if($user->isSuperAdmin())
            <p>
                <label for="company_name">Company Name</label><br>
                <input id="company_name" name="company_name" type="text" required value="{{ old('company_name') }}">
            </p>
        @endif

        <p>
            <label for="name">Name</label><br>
            <input id="name" name="name" type="text" required value="{{ old('name') }}">
        </p>

        <p>
            <label for="email">Email</label><br>
            <input id="email" name="email" type="email" required value="{{ old('email') }}">
        </p>

        <p>
            <label for="role">Role</label><br>
            <select id="role" name="role" required>
                @if($user->isSuperAdmin())
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                @else
                    <option value="member" @selected(old('role') === 'member')>Member</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                @endif
            </select>
        </p>

        <button type="submit">Send Invitation</button>
        <a href="{{ route('dashboard') }}">Cancel</a>
    </form>
</x-app-layout>
