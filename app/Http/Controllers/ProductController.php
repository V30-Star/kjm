<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\GroupProduct;
use App\Models\Merek;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
  public function index()
  {
    return view('product.index');
  }

  public function dataServer(Request $r)
  {
    $draw   = (int) $r->input('draw', 1);
    $start  = (int) $r->input('start', 0);
    $length = (int) $r->input('length', 10);
    $search = Str::of($r->input('search.value', ''))->trim()->value();

    $cols = ['id', 'name_barang', 'qty', 'harga_modal', 'harga_akhir', 'groupproduct_id', 'merek_id', 'created_date'];
    $orderIdx = (int) data_get($r->input('order', [0 => ['column' => 0]]), '0.column', 0);
    $orderCol = Arr::get($cols, $orderIdx, 'id');
    $orderDir = $r->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

    $q = Product::query()->select($cols);
    $recordsTotal = (clone $q)->count();

    if ($search !== '') {
      $q->where(function ($x) use ($search) {
        $x->where('name_barang', 'ILIKE', "%{$search}%")
          ->orWhere('groupproduct_id', 'ILIKE', "%{$search}%")
          ->orWhere('merek_id', 'ILIKE', "%{$search}%");
      });
    }

    $recordsFiltered = (clone $q)->count();

    $rows = $q->with(['groupproduct', 'merek'])
      ->orderBy($orderCol, $orderDir)
      ->skip($start)->take($length)->get()
      ->map(function ($p) {
        return [
          'id'           => $p->id,
          'name_barang'  => $p->name_barang,
          'qty'          => $p->qty,
          'harga_modal'  => number_format($p->harga_modal, 2, ',', '.'),
          'harga_akhir'  => number_format($p->harga_akhir, 2, ',', '.'),
          'groupproduct_id' => optional($p->groupproduct)->name_groupproduct,
          'merek_id'     => optional($p->merek)->name_merek,
          'created_date' => optional($p->created_date)->format('Y-m-d H:i'),
          'actions'      => view('product.partials.actions', ['p' => $p])->render(),
        ];
      });


    return response()->json([
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsFiltered,
      'data' => $rows
    ]);
  }

  public function create()
  {
    return view('product.create', [
      'groups' => GroupProduct::all(),
      'mereks' => Merek::all()
    ]);
  }

  public function store(Request $r)
  {
    $v = $r->validate([
      'name_barang' => ['required', 'string', 'max:255'],
      'qty' => ['required', 'integer', 'min:0'],
      'harga_modal' => ['required', 'numeric', 'min:0'],
      'harga_akhir' => ['required', 'numeric', 'min:0'],
      'groupproduct_id' => ['nullable', 'string', 'max:10'],
      'merek_id' => ['nullable', 'string', 'max:10'],
    ]);
    $user = Auth::user();
    $v['created_user'] = $user?->username ?? Auth::id();
    $v['updated_user'] = $v['created_user'];

    Product::create($v);
    return redirect()->route('product.index')->with('success', 'Product created.');
  }

  public function edit(Product $product)
  {
    return view('product.edit', [
      'p' => $product,
      'groups' => GroupProduct::all(),
      'mereks' => Merek::all()
    ]);
  }

  public function update(Request $r, Product $product)
  {
    $v = $r->validate([
      'name_barang' => ['required', 'string', 'max:255'],
      'qty' => ['required', 'integer', 'min:0'],
      'harga_modal' => ['required', 'numeric', 'min:0'],
      'harga_akhir' => ['required', 'numeric', 'min:0'],
      'groupproduct_id' => ['nullable', 'string', 'max:10'],
      'merek_id' => ['nullable', 'string', 'max:10'],
    ]);
    $user = Auth::user();
    $v['updated_user'] = $user?->username ?? Auth::id();

    $product->update($v);
    return redirect()->route('product.index')->with('success', 'Product updated.');
  }

  public function destroy(Product $product)
  {
    $product->delete();
    return redirect()->route('product.index')->with('success', 'Product deleted.');
  }
}
