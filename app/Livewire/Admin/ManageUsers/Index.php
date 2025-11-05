<?php


namespace App\Livewire\Admin\ManageUsers;

use Livewire\Component;
use App\Models\User as ModelUser;
use Illuminate\Support\Facades\Hash;


class Index extends Component
{
    public $pilihanMenu = 'lihat';
    public $nama;
    public $email;
    public $role;
    public $password;
    public $penggunaTerpilih;

    // 🔹 Pilih Menu
    public function pilihMenu($menu)
    {
        $this->reset(['nama', 'email', 'password', 'role', 'penggunaTerpilih']);
        $this->pilihanMenu = $menu;
    }

    // 🔹 Tambah Pengguna
    public function simpan()
    {
        $this->validate([
            'nama' => 'required|string',
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => 'required',
            'password' => 'required|min:8',
        ]);

        ModelUser::create([
            'name' => $this->nama,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);

        $this->resetForm();
    }

    // 🔹 Edit Pengguna
    public function pilihEdit($id)
    {
        $this->penggunaTerpilih = ModelUser::findOrFail($id);
        $this->nama = $this->penggunaTerpilih->name;
        $this->email = $this->penggunaTerpilih->email;
        $this->role = $this->penggunaTerpilih->role;
        $this->pilihanMenu = 'edit';
    }

    public function simpanEdit()
    {
        $this->validate([
            'nama' => 'required|string',
            'email' => ['required', 'email', 'unique:users,email,' . $this->penggunaTerpilih->id],
            'role' => 'required',
            'password' => 'nullable|min:8',
        ]);

        $update = $this->penggunaTerpilih;
        $update->name = $this->nama;
        $update->email = $this->email;
        $update->role = $this->role;
        if ($this->password) {
            $update->password = Hash::make($this->password);
        }

        $update->save();

        $this->resetForm();
    }

    // 🔹 Hapus Pengguna
    public function pilihHapus($id)
    {
        $this->penggunaTerpilih = ModelUser::findOrFail($id);
        $this->pilihanMenu = 'hapus';
    }

    public function hapus()
    {
        if ($this->penggunaTerpilih) {
            $this->penggunaTerpilih->delete();
        }
        $this->resetForm();
    }

    // 🔹 Reset
    public function batal()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['nama', 'email', 'password', 'role', 'penggunaTerpilih']);
        $this->pilihanMenu = 'lihat';
    }

    public function render()
    {
        return view('livewire.admin.manage-users.index', [
            'semuaPengguna' => ModelUser::all(),
        ]);
    }
}
