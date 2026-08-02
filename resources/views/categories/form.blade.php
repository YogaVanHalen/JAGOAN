@csrf
<div class="mb-4">
    <label for="name" class="block font-medium mb-1">Nama Kategori</label>
    <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}"
        class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-400">
</div>

<div class="mb-4">
    <label for="type" class="block font-medium mb-1">Tipe</label>
    <select name="type" id="type"
        class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-400">
        <option value="income" {{ old('type', $category->type ?? '') == 'income' ? 'selected' : '' }}>Pemasukan</option>
        <option value="expense" {{ old('type', $category->type ?? '') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
    </select>
</div>

<button type="submit"
    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl">
    {{ $submit ?? 'Simpan' }}
</button>
