{{-- resources/views/users/partials/actions.blade.php --}}
@props(['u'])

<div class="btn-group btn-group-sm" role="group">
    <a href="{{ route('users.edit', $u->id) }}" class="btn btn-outline-primary" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>

    <button type="button" class="btn btn-outline-danger" title="Delete" data-bs-toggle="modal"
        data-bs-target="#confirmDeleteModal" data-route="{{ route('users.destroy', $u->id) }}"
        data-name="{{ $u->username }}">
        <i class="bi bi-trash"></i>
    </button>
</div>
