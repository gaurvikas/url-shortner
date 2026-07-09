<x-app-layout>
    <x-slot name="header">
        <h1>Dashboard</h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
            <p><a href="{{ route('invitations.create') }}">Invite User</a></p>
        @endif
    </x-slot>

    @if(session('status'))
        <p>{{ session('status') }}</p>
        @if(session('invitation_url'))
            <p>
                Invitation link:
                <a href="{{ session('invitation_url') }}">{{ session('invitation_url') }}</a>
            </p>
        @endif
    @endif

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @if(auth()->user()->canCreateShortUrls())
        <section>
            <h2>Generate Short URL</h2>
            <form method="POST" action="{{ route('short-urls.store') }}">
                @csrf
                <p>
                    <label for="original_url">Long URL</label><br>
                    <input id="original_url" name="original_url" type="url" required size="80" placeholder="https://example.com/very/long/url" value="{{ old('original_url') }}">
                </p>
                <button type="submit">Generate</button>
            </form>
        </section>
    @elseif(auth()->user()->isSuperAdmin())
        <p>SuperAdmin can review URLs and invite company admins, but cannot create short URLs.</p>
    @endif

    @if(auth()->user()->isSuperAdmin())
        <section>
            <h2>Companies</h2>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Users</th>
                        <th>Generated URLs</th>
                        <th>URL Hits</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr>
                            <td>{{ $company->name }}</td>
                            <td>{{ $company->users_count }}</td>
                            <td>{{ $company->short_urls_count }}</td>
                            <td>{{ $company->short_urls_sum_visits ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No companies yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    @endif

    <section>
        <h2>Generated Short URLs</h2>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Short URL</th>
                    <th>Long URL</th>
                    <th>Hits</th>
                    <th>Role</th>
                    <th>Created By</th>
                    <th>Company</th>
                    <th>Created On</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shortUrls as $shortUrl)
                    <tr>
                        <td><a href="{{ $shortUrl->shortUrl() }}" target="_blank">{{ $shortUrl->shortUrl() }}</a></td>
                        <td>{{ $shortUrl->original_url }}</td>
                        <td>{{ $shortUrl->visits }}</td>
                        <td>{{ ucfirst($shortUrl->user->role) }}</td>
                        <td>{{ ucfirst($shortUrl->user->name) }}</td>
                        <td>{{ ucfirst($shortUrl->company->name) }}</td>
                        <td>{{ $shortUrl->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">No short URLs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $shortUrls->links() }}
    </section>

    @if(auth()->user()->isAdmin())
        <section>
            <h2>Team Members</h2>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Generated URLs</th>
                        <th>URL Hits</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teamMembers as $member)
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->email }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $member->role)) }}</td>
                            <td>{{ $member->short_urls_count }}</td>
                            <td>{{ $member->short_urls_sum_visits ?? 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif
</x-app-layout>
