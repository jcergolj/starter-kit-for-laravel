<ul>
    @forelse($clients as $client)
        <li data-turbo-action="replace" data-client-id="{{ $client->id }}">
            {{ $client->name }}
        </li>
    @empty
        <li>No client found.</li>
        <form action="{{ route('clients.store') }}" method="POST" data-turbo-frame="client-form">
            @csrf
            <input type="text" name="name" value="{{ $query }}" placeholder="Client Name" required>
            <input type="text" name="currency" placeholder="Currency" required>
            <input type="number" name="hourly_rate" placeholder="Hourly Rate" step="0.01" required>
            <button type="submit">Add Client</button>
        </form>
    @endforelse
</ul>
