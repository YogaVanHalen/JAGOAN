@extends('layouts.app')
@include('components.bottom-nav')
@section('content')
<div class="p-4">
    <h1 class="text-xl font-bold mb-4">Tambah Kategori</h1>
    <form action="{{ route('categories.store') }}" method="POST">
        @include('categories.form', ['submit' => 'Tambah'])
    </form>
</div>
@endsection