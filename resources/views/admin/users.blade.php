@extends('layouts.app')

@section('content')
<div class="px-4 py-6 sm:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs text-indigo-600 dark:text-indigo-400 font-bold hover:underline mb-1 inline-block">
                ← Kembali ke Dashboard Admin
            </a>
            <h2 class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">Manajemen Pengguna (User Level)</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Kelola role Superadmin, Admin (Wallet Owner), dan User Anggota.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button onclick="openModal('createUserModal')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-2xl transition flex items-center gap-1.5 shadow-sm">
                <span>➕</span> Tambah Pengguna Baru
            </button>

            <!-- Search Bar -->
            <form method="GET" action="{{ route('admin.users') }}" class="flex items-center gap-2">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari nama / email..." 
                    class="px-4 py-2 text-xs rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-64"
                >
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold text-xs rounded-2xl hover:bg-indigo-700 transition">
                    Cari
                </button>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 uppercase text-[10px] font-extrabold tracking-wider text-slate-400">
                    <tr>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Role / Level</th>
                        <th class="px-6 py-4">Terdaftar</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-500/10 text-indigo-600 font-bold flex items-center justify-center text-xs">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 dark:text-slate-100">{{ $user->name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $user->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->role === 'superadmin')
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold bg-purple-500/20 text-purple-600 dark:text-purple-300 border border-purple-400/40 rounded-lg">
                                        🛡️ SUPERADMIN
                                    </span>
                                @elseif($user->role === 'admin')
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold bg-amber-400/20 text-amber-600 dark:text-amber-400 border border-amber-400/40 rounded-lg">
                                        👑 ADMIN (Owner)
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-lg">
                                        👤 USER (Member)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($user->id === Auth::id())
                                    <span class="text-[11px] text-slate-400 italic">Akun Anda</span>
                                @else
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit User Button -->
                                        <button onclick="openEditModal({{ json_encode($user) }})" class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-300 font-bold text-xs rounded-lg hover:bg-indigo-100 transition border border-indigo-200/50">
                                            Edit
                                        </button>

                                        <!-- Toggle Role Button -->
                                        <form method="POST" action="{{ route('admin.users.toggleRole', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg font-bold text-xs border transition bg-slate-50 dark:bg-slate-800 text-slate-600 border-slate-200 hover:bg-slate-100" title="Ganti Role Cepat">
                                                Ganti Role
                                            </button>
                                        </form>

                                        <!-- Delete User Button -->
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna {{ $user->name }} ini secara permanen?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 font-bold text-xs rounded-lg hover:bg-rose-100 transition border border-rose-200/50">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                Tidak ada pengguna ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Pengguna Baru -->
<div id="createUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-md rounded-3xl shadow-2xl p-6 relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-base flex items-center gap-2">
                <span>👤</span> Tambah Pengguna Baru
            </h3>
            <button onclick="closeModal('createUserModal')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4 pt-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required placeholder="Contoh: Budi Santoso" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email *</label>
                <input type="email" name="email" required placeholder="budi@email.com" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Password *</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Role / Level Akses *</label>
                <select name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
                    <option value="user">👤 User (Member biasa)</option>
                    <option value="admin">👑 Admin (Wallet Owner)</option>
                    <option value="superadmin">🛡️ Superadmin (Kelola Seluruh Sistem)</option>
                </select>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="button" onclick="closeModal('createUserModal')" class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl text-xs">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-xl text-xs shadow-sm">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pengguna -->
<div id="editUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-md rounded-3xl shadow-2xl p-6 relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-base flex items-center gap-2">
                <span>✏️</span> Edit Pengguna
            </h3>
            <button onclick="closeModal('editUserModal')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
        </div>

        <form id="editUserForm" method="POST" class="space-y-4 pt-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                <input type="text" id="edit_name" name="name" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email *</label>
                <input type="email" id="edit_email" name="email" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Role / Level Akses *</label>
                <select id="edit_role" name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
                    <option value="user">👤 User (Member biasa)</option>
                    <option value="admin">👑 Admin (Wallet Owner)</option>
                    <option value="superadmin">🛡️ Superadmin (Kelola Seluruh Sistem)</option>
                </select>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="button" onclick="closeModal('editUserModal')" class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl text-xs">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl text-xs shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(user) {
    const form = document.getElementById('editUserForm');
    form.action = '/admin/users/' + user.id;
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    openModal('editUserModal');
}
</script>
@endsection
