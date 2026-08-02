@extends('layouts.app')

@section('content')
<div class="p-4">
    <h1 class="text-xl font-bold mb-4">Edit Kategori</h1>
    <form action="{{ route('categories.update', $category) }}" method="POST">
        @method('PUT')
        @include('categories.form', ['submit' => 'Update'])
    </form>
</div>
@endsection