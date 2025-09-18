<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function dataServer(Request $request)
    {
        $draw   = (int) $request->input('draw', 1);
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = Str::of($request->input('search.value', ''))->trim()->value();

        $columns = ['id', 'username', 'full_name', 'email', 'role', 'is_active', 'created_at'];
        $orderColIdx = (int) data_get($request->input('order', [0 => ['column' => 0]]), '0.column', 0);
        $orderCol = Arr::get($columns, $orderColIdx, 'id');
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

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
    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'full_name' => 'nullable|string|max:100',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        // pakai Hash dengan namespace yang benar
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username'   => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'full_name'  => ['nullable', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'password'   => ['nullable', 'min:6', 'confirmed'],
            'role'       => ['required', 'string'],
            'is_active'  => ['required', 'boolean'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
