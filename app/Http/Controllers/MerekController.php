<?php

namespace App\Http\Controllers;

use App\Models\Merek;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class MerekController extends Controller
{
  public function index()
  {
    return view('merek.index');
  }

  public function dataServer(Request $r)
  {
    $draw   = (int) $r->input('draw', 1);
    $start  = (int) $r->input('start', 0);
    $length = (int) $r->input('length', 10);
    $search = Str::of($r->input('search.value', ''))->trim()->value();

    $cols = ['id', 'kode_merek', 'name_merek', 'status', 'created_date'];
    $orderIdx = (int) data_get($r->input('order', [0 => ['column' => 0]]), '0.column', 0);
    $orderCol = Arr::get($cols, $orderIdx, 'id');
    $orderDir = $r->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

    $q = Merek::query()->select($cols);
    $recordsTotal = (clone $q)->count();

    if ($search !== '') {
      $q->where(function ($x) use ($search) {
        $x->where('kode_merek', 'ILIKE', "%{$search}%")
          ->orWhere('name_merek', 'ILIKE', "%{$search}%")
          ->orWhere('status', 'ILIKE', "%{$search}%");
      });
    }

    $recordsFiltered = (clone $q)->count();

    $rows = $q->orderBy($orderCol, $orderDir)
      ->skip($start)->take($length)->get()
      ->map(function ($m) {
        return [
          'id' => $m->id,
          'kode_merek' => $m->kode_merek,
          'name_merek' => $m->name_merek,
          'status' => $m->status,
          'created_date' => optional($m->created_date)->format('Y-m-d H:i'),
          'actions' => view('merek.partials.actions', ['m' => $m])->render(),
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
    return view('merek.create');
  }

  public function store(Request $r)
  {
    $v = $r->validate([
      'kode_merek' => ['required', 'string', 'max:255', 'unique:merek,kode_merek'],
      'name_merek' => ['required', 'string', 'max:255'],
      'status' => ['required', 'string', 'max:2'],
    ]);
    $user = Auth::user();
    $v['created_user'] = $user?->username ?? Auth::id();
    $v['updated_user'] = $v['created_user'];

    Merek::create($v);
    return redirect()->route('merek.index')->with('success', 'Merek created.');
  }

  public function edit(Merek $merek)
  {
    return view('merek.edit', ['m' => $merek]);
  }

  public function update(Request $r, Merek $merek)
  {
    $v = $r->validate([
      'kode_merek' => ['required', 'string', 'max:255', Rule::unique('merek', 'kode_merek')->ignore($merek->id)],
      'name_merek' => ['required', 'string', 'max:255'],
      'status' => ['required', 'string', 'max:2'],
    ]);
    $user = Auth::user();
    $v['updated_user'] = $user?->username ?? Auth::id();

    $merek->update($v);
    return redirect()->route('merek.index')->with('success', 'Merek updated.');
  }

  public function destroy(Merek $merek)
  {
    $merek->delete();
    return redirect()->route('merek.index')->with('success', 'Merek deleted.');
  }
}
