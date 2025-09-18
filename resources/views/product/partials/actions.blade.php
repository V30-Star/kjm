@props(['p'])

<div class="btn-group btn-group-sm" role="group">
    {{-- Edit --}}
    <a href="{{ route('product.edit', $p->id) }}" class="btn btn-outline-primary" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>

    {{-- Delete --}}
    <button type="button" class="btn btn-outline-danger" title="Delete" data-bs-toggle="modal"
        data-bs-target="#productDeleteModal" data-route="{{ route('product.destroy', $p->id) }}"
        data-name="{{ $p->name_barang }}">
        <i class="bi bi-trash"></i>
    </button>
</div>
