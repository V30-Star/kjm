<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function dataServer(Request $request)
    {
        // DataTables params
        $draw   = (int) $request->input('draw', 1);
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = Str::of($request->input('search.value', ''))->trim()->value();

        // Whitelist columns for ordering
        $columns = ['id', 'username', 'full_name', 'email', 'role', 'is_active', 'created_at'];
        $orderColIdx = (int) data_get($request->input('order', [0 => ['column' => 0]]), '0.column', 0);
        $orderCol = Arr::get($columns, $orderColIdx, 'id');
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

        // Base query
        $base = User::query()->select($columns);

        // Total records
        $recordsTotal = (clone $base)->count();

        // Global search
        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('username', 'ILIKE', "%{$search}%")
                    ->orWhere('full_name', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%")
                    ->orWhere('role', 'ILIKE', "%{$search}%");
            });
        }

        // Example of column filters (optional—add from request if you add inputs)
        // if ($request->filled('role'))   $base->where('role', $request->role);
        // if ($request->filled('active')) $base->where('is_active', (bool)$request->active);

        $recordsFiltered = (clone $base)->count();

        // Paging + ordering
        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)
            ->take($length)
            ->get()
            ->map(function ($u) {
                return [
                    'id'         => $u->id,
                    'username'   => $u->username,
                    'full_name'  => $u->full_name,
                    'email'      => $u->email,
                    'role'       => $u->role,
                    'is_active'  => $u->is_active,
                    'created_at' => optional($u->created_at)->format('Y-m-d H:i'),
                    'actions'    => view('users.partials.actions', ['u' => $u])->render(),
                ];
            });

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $rows,
        ]);
    }
}
