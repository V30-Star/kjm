<?php

namespace App\Http\Controllers;

use App\Models\GroupProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;


class GroupproductController extends Controller
{
  public function index()
  {
    return view('groupproduct.index');
  }

  public function dataServer(Request $r)
  {
    $draw   = (int) $r->input('draw', 1);
    $start  = (int) $r->input('start', 0);
    $length = (int) $r->input('length', 10);
    $search = Str::of($r->input('search.value', ''))->trim()->value();

    $cols = ['id', 'kode_groupproduct', 'name_groupproduct', 'status', 'created_date'];
    $orderIdx = (int) data_get($r->input('order', [0 => ['column' => 0]]), '0.column', 0);
    $orderCol = Arr::get($cols, $orderIdx, 'id');
    $orderDir = $r->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

    $q = GroupProduct::query()->select($cols);

    $recordsTotal = (clone $q)->count();

    if ($search !== '') {
      $q->where(function ($x) use ($search) {
        $x->where('kode_groupproduct', 'ILIKE', "%{$search}%")
          ->orWhere('name_groupproduct', 'ILIKE', "%{$search}%")
          ->orWhere('status', 'ILIKE', "%{$search}%");
      });
    }

    $recordsFiltered = (clone $q)->count();

    $rows = $q->orderBy($orderCol, $orderDir)
      ->skip($start)->take($length)->get()
      ->map(function ($g) {
        return [
          'id' => $g->id,
          'kode_groupproduct' => $g->kode_groupproduct,
          'name_groupproduct' => $g->name_groupproduct,
          'status' => $g->status,
          'created_date' => optional($g->created_date)->format('Y-m-d H:i'),
          'actions' => view('groupproduct.partials.actions', ['g' => $g])->render(),
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
    return view('groupproduct.create');
  }

  public function store(Request $r)
  {
    $v = $r->validate([
      'kode_groupproduct' => ['required', 'string', 'max:255', 'unique:groupproduct,kode_groupproduct'],
      'name_groupproduct' => ['required', 'string', 'max:255'],
      'status'            => ['required', 'string', 'max:2'], // 'A' / 'I'
    ]);
    $user = Auth::user();
    $v['created_user'] = $user?->username ?? Auth::id();
    $v['updated_user'] = $v['created_user'];

    GroupProduct::create($v);
    return redirect()->route('groupproduct.index')->with('success', 'Group created.');
  }

  public function edit(GroupProduct $gp)
  {
    return view('groupproduct.edit', ['gp' => $gp]);
  }

  public function update(Request $r, GroupProduct $gp)
  {
    $v = $r->validate([
      'kode_groupproduct' => ['required', 'string', 'max:255', Rule::unique('groupproduct', 'kode_groupproduct')->ignore($gp->id)],
      'name_groupproduct' => ['required', 'string', 'max:255'],
      'status'            => ['required', 'string', 'max:2'],
    ]);
    $user = Auth::user();
    $v['updated_user'] = $user?->username ?? Auth::id();

    $gp->update($v);
    return redirect()->route('groupproduct.index')->with('success', 'Group updated.');
  }

  public function destroy(GroupProduct $gp)
  {
    $gp->delete();
    return redirect()->route('groupproduct.index')->with('success', 'Group deleted.');
  }
}
