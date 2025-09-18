@props(['m'])

<div class="btn-group btn-group-sm" role="group">
    <a href="{{ route('merek.edit', $m->id) }}" class="btn btn-outline-primary" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>
    <button type="button" class="btn btn-outline-danger" title="Delete" data-bs-toggle="modal"
        data-bs-target="#merekDeleteModal" data-route="{{ route('merek.destroy', $m->id) }}"
        data-name="{{ $m->name_merek }}">
        <i class="bi bi-trash"></i>
    </button>
</div>
