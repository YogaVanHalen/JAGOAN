@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300 pb-24">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">🏷️ Kelola Kategori</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelompokkan Pemasukan dan Pengeluaran Anda</p>
        </div>
        <button onclick="openModal('createCategoryModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold shadow-sm transition flex items-center gap-1.5">
            <span>➕</span> Tambah Kategori
        </button>
    </div>

    <!-- Category List Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($categories as $category)
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 rounded-2xl shadow-sm hover:shadow-md transition flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl {{ $category->type === 'income' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400' }} flex items-center justify-center font-extrabold text-sm">
                        {{ $category->type === 'income' ? '↓' : '↑' }}
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm sm:text-base">{{ $category->name }}</h3>
                        <span class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-md {{ $category->type === 'income' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-400' : 'bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-400' }}">
                            {{ $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" onclick="openEditCategoryModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ $category->type }}')" class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition border border-indigo-200/50 dark:border-indigo-800/40 leading-none">
                        Edit
                    </button>

                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ addslashes($category->name) }}?');" class="inline-flex items-center m-0 p-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition border border-rose-200/50 dark:border-rose-800/40 leading-none">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @endforeach

        @if($categories->isEmpty())
            <div class="sm:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-8 rounded-2xl text-center">
                <span class="text-3xl">🏷️</span>
                <h3 class="font-bold text-slate-700 dark:text-slate-200 mt-2">Belum Ada Kategori</h3>
                <p class="text-xs text-slate-400 mt-1">Buat kategori baru seperti Gaji, Makanan, Transportasi, atau Tagihan.</p>
                <button onclick="openModal('createCategoryModal')" class="inline-block mt-4 text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl font-semibold transition">
                    + Tambah Kategori Pertama
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Modal 1: Tambah Kategori Popup -->
<div id="createCategoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-md rounded-3xl shadow-2xl p-6 relative animate-in fade-in zoom-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                    🏷️
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-lg">Tambah Kategori Baru</h3>
                    <p class="text-xs text-slate-400">Kategori untuk pengelompokan transaksi</p>
                </div>
            </div>
            <button onclick="closeModal('createCategoryModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold p-1">✕</button>
        </div>

        <form action="{{ route('categories.store') }}" method="POST" class="space-y-4 pt-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Kategori *</label>
                <input type="text" name="name" required placeholder="Contoh: Makanan & Minuman, Gaji & Bonus" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Transaksi *</label>
                <select name="type" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
                    <option value="expense" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Pengeluaran (Expense)</option>
                    <option value="income" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Pemasukan (Income)</option>
                </select>
            </div>

            <div class="pt-3 flex gap-3">
                <button type="button" onclick="closeModal('createCategoryModal')" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl text-xs transition shadow-sm">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Edit Kategori Popup -->
<div id="editCategoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-md rounded-3xl shadow-2xl p-6 relative animate-in fade-in zoom-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-950/80 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                    ✏️
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-lg">Edit Kategori</h3>
                    <p class="text-xs text-slate-400">Ubah rincian kategori transaksi</p>
                </div>
            </div>
            <button onclick="closeModal('editCategoryModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold p-1">✕</button>
        </div>

        <form id="editCategoryForm" action="" method="POST" class="space-y-4 pt-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Kategori *</label>
                <input type="text" name="name" id="editCategoryName" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Transaksi *</label>
                <select name="type" id="editCategoryType" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
                    <option value="expense" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Pengeluaran (Expense)</option>
                    <option value="income" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Pemasukan (Income)</option>
                </select>
            </div>

            <div class="pt-3 flex gap-3">
                <button type="button" onclick="closeModal('editCategoryModal')" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 rounded-xl text-xs transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditCategoryModal(id, name, type) {
    const form = document.getElementById('editCategoryForm');
    const nameInput = document.getElementById('editCategoryName');
    const typeSelect = document.getElementById('editCategoryType');

    if (form && nameInput && typeSelect) {
        form.action = '/categories/' + id;
        nameInput.value = name;
        typeSelect.value = type;
        openModal('editCategoryModal');
    }
}
</script>
@endsection