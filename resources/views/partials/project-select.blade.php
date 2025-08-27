<select 
    name="project_id" 
    id="project_select"
    class="w-full input data-error:input-error min-w-[150px]"
>
    <option value="">Select Project</option>
    @foreach($projects as $project)
        <option value="{{ $project->id }}" data-client-id="{{ $project->client_id }}">
            {{ $project->name }}
            @if($project->client)
                ({{ $project->client->name }})
            @endif
        </option>
    @endforeach
</select>