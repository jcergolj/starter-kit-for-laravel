<select 
    name="client_id" 
    id="client_select"
    class="w-full input data-error:input-error min-w-[150px]"
    onchange="updateProjects()"
>
    <option value="">Select Client</option>
    @foreach($clients as $client)
        <option value="{{ $client->id }}">{{ $client->name }}</option>
    @endforeach
</select>