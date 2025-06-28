<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User; // Pastikan menggunakan model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SAController extends Controller
{
    /**
     * Display a listing of the employees (Admins).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil semua user dengan role 'admin'
        $employees = User::where('role', 'admin')->orderBy('name')->get();
        return view('frontend.admin.employees.index', compact('employees'));
    }

    /**
     * Store a newly created employee (Admin) in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'hp' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'boolean', // Akan mengonversi 'true'/'false' dari checkbox
        ]);

        try {
            User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'hp' => $validatedData['hp'],
                'role' => 'admin', // Otomatis set role sebagai admin
                'status' => $validatedData['status'] ?? false, // Default false jika tidak ada
            ]);

            return response()->json(['success' => true, 'message' => 'Karyawan (Admin) berhasil ditambahkan!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan karyawan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified employee (Admin) in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $employee
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $employee)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($employee->id), // Ignore current user for unique rule
            ],
            'hp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed', // Nullable for optional password change
            'status' => 'boolean',
        ]);

        try {
            $employee->name = $validatedData['name'];
            $employee->email = $validatedData['email'];
            $employee->hp = $validatedData['hp'];
            $employee->status = $validatedData['status'] ?? false;

            if (!empty($validatedData['password'])) {
                $employee->password = Hash::make($validatedData['password']);
            }
            // Role tetap 'admin', tidak bisa diubah dari sini
            $employee->role = 'admin';

            $employee->save();

            return response()->json(['success' => true, 'message' => 'Data karyawan (Admin) berhasil diperbarui!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui data karyawan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified employee (Admin) from storage.
     *
     * @param  \App\Models\User  $employee
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $employee)
    {
        try {
            // Pastikan tidak menghapus akun admin yang sedang login (opsional, tapi disarankan)
            if (Auth::user() === $employee->id) {
                return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
            }

            // Pastikan hanya admin yang bisa menghapus admin lain (jika ada hierarki super admin)
            if ($employee->role !== 'admin') {
                return redirect()->back()->with('error', 'Hanya dapat menghapus karyawan dengan role admin.');
            }
            
            $employee->delete();
            return redirect()->back()->with('success', 'Karyawan (Admin) berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus karyawan: ' . $e->getMessage());
        }
    }
}
