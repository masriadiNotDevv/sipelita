<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    /**
     * Peran yang dapat dikelola dari halaman ini.
     *
     * @var array<int, string>
     */
    private const ROLES = [
        UserRole::Admin->value,
        UserRole::Staff->value,
    ];

    /**
     * Tampilkan daftar admin dan staff.
     */
    public function index(Request $request): View
    {
        $daftar = User::query()
            ->whereIn('role', self::ROLES)
            ->when($request->filled('cari'), function ($query) use ($request): void {
                $cari = '%'.trim((string) $request->string('cari')).'%';

                $query->where(function ($inner) use ($cari): void {
                    $inner->where('name', 'like', $cari)
                        ->orWhere('email', 'like', $cari);
                });
            })
            ->orderByRaw('CASE role WHEN ? THEN 0 ELSE 1 END', [UserRole::Admin->value])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.staff.index', [
            'daftar' => $daftar,
            'cari' => $request->string('cari')->toString(),
            'totalAdmin' => User::query()->where('role', UserRole::Admin->value)->count(),
            'totalStaff' => User::query()->where('role', UserRole::Staff->value)->count(),
        ]);
    }

    /**
     * Tampilkan formulir tambah staff.
     */
    public function create(): View
    {
        return view('admin.staff.create');
    }

    /**
     * Simpan akun staff atau admin baru.
     */
    public function store(StoreStaffRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'role']);
        $data['password'] = Hash::make($request->string('password')->toString());
        $data['email_verified_at'] = now();

        $staff = User::create($data);

        return redirect()
            ->route('admin.staff.index')
            ->with('status', "Akun {$staff->userRole()->label()} {$staff->name} berhasil ditambahkan.");
    }

    /**
     * Tampilkan formulir edit staff.
     */
    public function edit(User $staff): View
    {
        return view('admin.staff.edit', [
            'staff' => $staff,
        ]);
    }

    /**
     * Perbarui akun staff atau admin.
     */
    public function update(UpdateStaffRequest $request, User $staff): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'role']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->string('password')->toString());
        }

        $staff->update($data);

        return redirect()
            ->route('admin.staff.index')
            ->with('status', "Akun {$staff->name} berhasil diperbarui.");
    }

    /**
     * Hapus akun staff atau admin.
     */
    public function destroy(Request $request, User $staff): RedirectResponse
    {
        if ($request->user()->is($staff)) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $nama = $staff->name;
        $staff->delete();

        return redirect()
            ->route('admin.staff.index')
            ->with('status', "Akun {$nama} berhasil dihapus.");
    }
}
