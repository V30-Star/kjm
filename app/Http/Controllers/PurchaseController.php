<?php
// app/Http/Controllers/PurchaseController.php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
  public function index()
  {
    return view('pembelian.index');
  }

  // Server-side DataTables sederhana
  public function data(Request $r)
  {
    $draw   = (int) $r->input('draw', 1);
    $start  = (int) $r->input('start', 0);
    $length = (int) $r->input('length', 10);
    $search = trim($r->input('search.value', ''));

    $q = Purchase::query()->select(['id', 'no_transaksi', 'tanggal', 'nama_pembeli', 'total_qty', 'total_harga', 'created_date']);

    $recordsTotal = (clone $q)->count();
    if ($search !== '') {
      $q->where(function ($x) use ($search) {
        $x->where('no_transaksi', 'ILIKE', "%{$search}%")
          ->orWhere('nama_pembeli', 'ILIKE', "%{$search}%");
      });
    }
    $recordsFiltered = (clone $q)->count();

    $rows = $q->orderBy('id', 'desc')->skip($start)->take($length)->get()->map(function ($p) {
      return [
        'id' => $p->id,
        'no_transaksi' => $p->no_transaksi,
        'tanggal' => $p->tanggal,
        'nama_pembeli' => $p->nama_pembeli,
        'total_qty' => $p->total_qty,
        'total_harga' => number_format($p->total_harga, 2, ',', '.'),
        'created_date' => optional($p->created_date)->format('Y-m-d H:i'),
        'actions' => view('pembelian.partials.actions', ['p' => $p])->render(),
      ];
    });

    return response()->json([
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsFiltered,
      'data' => $rows,
    ]);
  }

  public function create()
  {
    return view('pembelian.create'); // Select2 pakai AJAX untuk produk
  }

  // Generate nomor transaksi: PB-YYYYMMDD-XXXX
  private function generateNoTransaksi(): string
  {
    $prefix = 'KJM-' . now()->format('Ymd') . '-';
    $last = Purchase::where('no_transaksi', 'like', $prefix . '%')
      ->orderByDesc('id')->value('no_transaksi');
    $n = $last ? ((int) Str::after($last, $prefix)) + 1 : 1;
    return $prefix . str_pad((string)$n, 4, '0', STR_PAD_LEFT);
  }

  public function store(Request $r)
  {
    $v = $r->validate([
      'tanggal'   => ['required', 'date'],
      'nama_pembeli'  => ['required', 'string', 'max:255'],
      'keterangan' => ['nullable', 'string'],
      'items'     => ['required', 'array', 'min:1'],
      'items.*.product_id' => ['required', 'integer', 'exists:product,id'],
      'items.*.qty'        => ['required', 'integer', 'min:1'],
      'items.*.harga_modal' => ['required', 'numeric', 'min:0'],
      'items.*.diskon' => ['nullable', 'numeric', 'min:0', 'max:100'],
    ]);

    DB::transaction(function () use ($v) {
      $user = Auth::user();
      $no   = $this->generateNoTransaksi();

      $totalQty = 0;
      $totalHarga = 0;

      // Ambil semua produk yang dipakai + KUNCI baris stoknya
      $mapItems   = collect($v['items']);
      $productIds = $mapItems->pluck('product_id')->all();
      $products   = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

      // Validasi stok dulu (qty harus cukup & tidak 0)
      foreach ($mapItems as $idx => $it) {
        $prod = $products->get($it['product_id']);
        $reqQ = (int)$it['qty'];

        if (!$prod) {
          throw ValidationException::withMessages(["items.$idx.product_id" => 'Produk tidak ditemukan.']);
        }
        if ($prod->qty <= 0) {
          throw ValidationException::withMessages(["items.$idx.qty" => "Stok {$prod->name_barang} habis (0)."]);
        }
        if ($prod->qty < $reqQ) {
          throw ValidationException::withMessages(["items.$idx.qty" => "Stok {$prod->name_barang} tersisa {$prod->qty}, tidak cukup."]);
        }
      }

      $p = Purchase::create([
        'no_transaksi' => $no,
        'tanggal'      => $v['tanggal'],
        'nama_pembeli'     => $v['nama_pembeli'],
        'keterangan'   => $v['keterangan'] ?? null,
        'total_qty'    => 0,
        'total_harga'  => 0,
        'created_user' => $user?->username ?? Auth::id(),
        'updated_user' => $user?->username ?? Auth::id(),
      ]);

      foreach ($mapItems as $it) {
        $prod   = $products->get($it['product_id']);
        $qty    = (int) $it['qty'];
        $harga  = (float) $it['harga_modal'];
        $diskon = isset($it['diskon']) ? max(0, min(100, (float)$it['diskon'])) : 0.0;

        $sub = $qty * $harga * (1 - ($diskon / 100));

        PurchaseItem::create([
          'pembelian_id'   => $p->id,
          'product_id'     => $prod->id,
          'qty'            => $qty,
          'harga_modal'    => $harga,
          'diskon_percent' => $diskon,
          'subtotal'       => $sub,
        ]);

        $prod->decrement('qty', $qty);

        $totalQty   += $qty;
        $totalHarga += $sub;
      }

      $diskonGlobal = max(0, min(100, (float) request()->input('__diskon_percent', 0)));
      $grandTotal   = $totalHarga * (1 - ($diskonGlobal / 100));

      $p->update([
        'total_qty'      => $totalQty,
        'total_harga'    => $grandTotal,
        'diskon_percent' => $diskonGlobal,
      ]);
    });

    return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil disimpan.');
  }

  public function edit(Purchase $purchase)
  {
    $purchase->load('items.product');

    $prefillItems = $purchase->items->map(function ($d) {
      return [
        'id'     => $d->product_id,
        'text'   => optional($d->product)->name_barang,
        'qty'    => $d->qty,
        'harga'  => $d->harga_modal,
        'diskon' => $d->diskon_percent ?? 0,
        'stock'  => optional($d->product)->qty,
      ];
    })->values();

    return view('pembelian.edit', compact('purchase', 'prefillItems'));
  }

  public function update(Request $r, Purchase $purchase)
  {
    $v = $r->validate([
      'tanggal'            => ['required', 'date'],
      'nama_pembeli'       => ['required', 'string', 'max:255'],
      'keterangan'         => ['nullable', 'string'],
      'items'              => ['required', 'array', 'min:1'],
      'items.*.product_id' => ['required', 'integer', 'exists:product,id'],
      'items.*.qty'        => ['required', 'integer', 'min:1'],
      'items.*.harga_modal' => ['required', 'numeric', 'min:0'],
      'items.*.diskon'     => ['nullable', 'numeric', 'min:0', 'max:100'],
    ]);

    DB::transaction(function () use ($v, $purchase, $r) {
      $user = Auth::user();

      // 1) Kunci semua product yang terlibat (lama + baru)
      $oldItems = $purchase->items()->get(['product_id', 'qty']); // detail lama
      $oldMap   = $oldItems->groupBy('product_id')->map(fn($c) => (int)$c->sum('qty'))->all();

      $newItems = collect($v['items']);
      $newIds   = $newItems->pluck('product_id')->all();

      $lockIds  = array_values(array_unique(array_merge(array_keys($oldMap), $newIds)));
      $products = Product::whereIn('id', $lockIds)->lockForUpdate()->get()->keyBy('id');

      // 2) Kembalikan stok lama (rollback stok dari transaksi sebelumnya)
      foreach ($oldMap as $pid => $qtyWas) {
        if ($p = $products->get($pid)) {
          $p->increment('qty', $qtyWas);
        }
      }

      // 3) Validasi stok baru cukup
      foreach ($newItems as $idx => $it) {
        $pid = (int)$it['product_id'];
        $req = (int)$it['qty'];
        $p   = $products->get($pid);

        if (!$p) {
          throw ValidationException::withMessages(["items.$idx.product_id" => 'Produk tidak ditemukan.']);
        }
        if ($p->qty <= 0) {
          throw ValidationException::withMessages(["items.$idx.qty" => "Stok {$p->name_barang} habis (0)."]);
        }
        if ($p->qty < $req) {
          throw ValidationException::withMessages(["items.$idx.qty" => "Stok {$p->name_barang} tersisa {$p->qty}, tidak cukup."]);
        }
      }

      // 4) Update header
      $purchase->update([
        'tanggal'      => $v['tanggal'],
        'nama_pembeli' => $v['nama_pembeli'],
        'keterangan'   => $v['keterangan'] ?? null,
        'updated_user' => $user?->username ?? Auth::id(),
      ]);

      // 5) Hapus detail lama, simpan detail baru + kurangi stok
      $purchase->items()->delete();

      $totalQty = 0;
      $subTotal = 0;

      foreach ($newItems as $it) {
        $pid    = (int)$it['product_id'];
        $qty    = (int)$it['qty'];
        $harga  = (float)$it['harga_modal'];
        $diskon = isset($it['diskon']) ? max(0, min(100, (float)$it['diskon'])) : 0.0;

        $sub = $qty * $harga * (1 - ($diskon / 100));

        PurchaseItem::create([
          'pembelian_id'   => $purchase->id,
          'product_id'     => $pid,
          'qty'            => $qty,
          'harga_modal'    => $harga,
          'diskon_percent' => $diskon,
          'subtotal'       => $sub,
        ]);

        // kurangi stok sekarang (aman karena sudah di-lock & stok baru sudah dikembalikan)
        $products[$pid]->decrement('qty', $qty);

        $totalQty += $qty;
        $subTotal += $sub;
      }

      $diskonGlobal = max(0, min(100, (float)$r->input('__diskon_percent', 0)));
      $grandTotal   = $subTotal * (1 - ($diskonGlobal / 100));

      $purchase->update([
        'total_qty'      => $totalQty,
        'total_harga'    => $grandTotal,
        'diskon_percent' => $diskonGlobal,
      ]);
    });

    return redirect()->route('pembelian.index')->with('success', 'Pembelian diperbarui.');
  }

  public function print(Purchase $pembelian)
  {
    $pembelian->load('items.product');
    return view('pembelian.print', compact('pembelian'));
  }

  public function destroy(Purchase $purchase)
  {
    $purchase->delete();
    return redirect()->route('pembelian.index')->with('success', 'Pembelian dihapus.');
  }

  // Ajax Select2 produk (cari cepat)
  public function searchProducts(Request $r)
  {
    $term = trim($r->input('term', ''));
    $q = Product::query()->select(['id', 'name_barang', 'harga_modal', 'qty'])->orderBy('name_barang');
    if ($term !== '') $q->where('name_barang', 'ILIKE', "%{$term}%");

    $data = $q->limit(30)->get()->map(fn($p) => [
      'id' => $p->id,
      'text' => $p->name_barang,
      'harga_modal' => $p->harga_modal,
      'stock'       => (int) $p->qty,
    ]);
    return response()->json(['results' => $data]);
  }
}
