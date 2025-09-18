{{-- resources/views/pembelian/partials/actions.blade.php --}}
@props(['p'])
<div class="btn-group btn-group-sm" role="group">
    <a href="{{ route('pembelian.edit', $p->id) }}" class="btn btn-outline-primary" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>
    <a href="{{ route('pembelian.print', $p->id) }}" target="_blank" class="btn btn-outline-success" title="Print">
        <i class="bi bi-printer"></i>
    </a>
    <button type="button" class="btn btn-outline-danger" title="Delete" data-bs-toggle="modal"
        data-bs-target="#purchaseDeleteModal" data-route="{{ route('pembelian.destroy', $p->id) }}"
        data-no="{{ $p->no_transaksi }}">
        <i class="bi bi-trash"></i>
    </button>
</div>
