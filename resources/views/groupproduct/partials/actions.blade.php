@props(['g'])

<div class="btn-group btn-group-sm" role="group">
    <a href="{{ route('groupproduct.edit', $g->id) }}" class="btn btn-outline-primary" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>
    <button type="button" class="btn btn-outline-danger" title="Delete" data-bs-toggle="modal"
        data-bs-target="#gpDeleteModal" data-route="{{ route('groupproduct.destroy', $g->id) }}"
        data-name="{{ $g->name_groupproduct }}">
        <i class="bi bi-trash"></i>
    </button>
</div>
