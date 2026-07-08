<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\User;
use App\Models\MasterAktivitasRutin;
use App\Models\MasterProyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Menampilkan daftar user dengan tombol aksi
     */
    public function kelolaUser()
    {
        $users = User::with('departemen')->get();
        $departemen = Departemen::all();
        return view('admin.user_index', compact('users', 'departemen'));
    }

    /**
     * Menampilkan form tambah user
     */
    public function createUser()
    {
        $departemen = Departemen::all();
        return view('admin.user_form', compact('departemen'));
    }

    /**
     * Menyimpan user baru
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'departemen_id' => 'required|exists:departemen,id',
            'password' => 'required|min:6|string',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'departemen_id' => $request->departemen_id,
            'is_atasan' => $request->has('is_atasan'),
            'is_admin' => $request->has('is_admin'),
        ]);

        return redirect()->route('admin.user')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit user
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $departemen = Departemen::all();
        return view('admin.user_form', compact('user', 'departemen'));
    }

    /**
     * Mengupdate user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'departemen_id' => 'required|exists:departemen,id',
            'password' => 'nullable|min:6|string',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'departemen_id' => $request->departemen_id,
            'is_atasan' => $request->has('is_atasan'),
            'is_admin' => $request->has('is_admin'),
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);

        return redirect()->route('admin.user')->with('success', 'User "' . $user->name . '" berhasil diupdate.');
    }

    /**
     * Menampilkan form assign aktivitas ke user
     */
    public function formAssignAktivitas()
    {
        $users = User::with('departemen')->get();
        $departemen = Departemen::all();
        return view('admin.assign_aktivitas', compact('users', 'departemen'));
    }

    /**
     * Proses assign aktivitas ke user (langsung approved)
     */
    public function assignAktivitas(Request $request)
    {
        // Validasi dasar
        $request->validate([
            'jenis' => 'required|in:rutin,proyek',
            'nama' => 'required|string|max:255',
            'assign_type' => 'required|in:specific_user,department',
            'bobot' => 'nullable|numeric|min:0|max:100',
        ]);

        // Validasi berdasarkan tipe assign
        if ($request->assign_type === 'specific_user') {
            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);
            $targetUsers = User::where('id', $request->user_id)->get();
        } else {
            $request->validate([
                'departemen_id' => 'required|exists:departemen,id'
            ]);
            $targetUsers = User::where('departemen_id', $request->departemen_id)->get();
        }

        // Validasi untuk proyek
        if ($request->jenis === 'proyek') {
            $request->validate([
                'tgl_mulai' => 'required|date',
                'tgl_berakhir' => 'required|date|after_or_equal:tgl_mulai',
            ]);
        }

        // Validasi untuk rutin
        if ($request->jenis === 'rutin') {
            $request->validate([
                'periode' => 'required|in:daily,weekly,monthly'
            ]);
        }

        // Jika tidak ada user target
        if ($targetUsers->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada user yang dipilih.');
        }

        $countCreated = 0;

        // Buat aktivitas untuk setiap user target
        foreach ($targetUsers as $user) {
            if ($request->jenis === 'rutin') {
                MasterAktivitasRutin::create([
                    'nama_aktivitas' => $request->nama,
                    'periode' => $request->periode,
                    'bobot' => $request->bobot ?? 10,
                    'departemen_id' => $user->departemen_id,
                    'user_id' => $targetUser->id,
                    'created_by' => auth()->id(),
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                ]);
                $countCreated++;
            } else {
                MasterProyek::create([
                    'nama_proyek' => $request->nama,
                    'tgl_mulai' => $request->tgl_mulai,
                    'tgl_berakhir' => $request->tgl_berakhir,
                    'bobot' => $request->bobot ?? 50,
                    'departemen_id' => $user->departemen_id,
                    'user_id' => $targetUser->id,
                    'created_by' => auth()->id(),
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                ]);
                $countCreated++;
            }
        }

        $message = $countCreated . ' aktivitas "' . $request->nama . '" berhasil diberikan ke ' . $targetUsers->count() . ' user(s).';
        return redirect()->route('dashboard')->with('success', $message);
    }

    /**
     * Menghapus user (opsional)
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Cegah menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        
        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    /**
     * Menghapus departemen (opsional)
     */
    public function deleteDepartemen($id)
    {
        $departemen = Departemen::findOrFail($id);
        
        // Cek apakah ada user di departemen ini
        if ($departemen->users()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus departemen yang masih memiliki user.');
        }
        
        $departemen->delete();
        return redirect()->back()->with('success', 'Departemen berhasil dihapus.');
    }

    /**
     * Menampilkan daftar departemen dengan tombol aksi
     */
    public function kelolaDepartemen()
    {
        $departemen = Departemen::all();
        return view('admin.departemen_index', compact('departemen'));
    }

    /**
     * Menampilkan form tambah departemen
     */
    public function createDepartemen()
    {
        return view('admin.departemen_form');
    }

    /**
     * Menyimpan departemen baru
     */
    public function storeDepartemen(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:departemen,nama',
            'bobot_default' => 'required|numeric|min:0|max:100'
        ]);

        Departemen::create([
            'nama' => $request->nama,
            'bobot_default' => $request->bobot_default
        ]);

        return redirect()->route('admin.departemen')
            ->with('success', 'Departemen "' . $request->nama . '" berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit departemen
     */
    public function editDepartemen($id)
    {
        $departemen = Departemen::findOrFail($id);
        return view('admin.departemen_form', compact('departemen'));
    }

    /**
     * Mengupdate departemen
     */
    public function updateDepartemen(Request $request, $id)
    {
        $departemen = Departemen::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:100|unique:departemen,nama,' . $id,
            'bobot_default' => 'required|numeric|min:0|max:100'
        ]);

        $departemen->update([
            'nama' => $request->nama,
            'bobot_default' => $request->bobot_default
        ]);

        return redirect()->route('admin.departemen')
            ->with('success', 'Departemen "' . $request->nama . '" berhasil diupdate.');
    }    
}