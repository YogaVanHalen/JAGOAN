<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function ensureDefaultCategoriesExist($userId)
    {
        if (!$userId) return;

        if (self::where('user_id', $userId)->count() === 0) {
            $defaultCategories = [
                ['name' => 'Gaji & Bonus', 'type' => 'income'],
                ['name' => 'Freelance & Usaha', 'type' => 'income'],
                ['name' => 'Investasi & Tabungan', 'type' => 'income'],
                ['name' => 'Lain-lain (Pemasukan)', 'type' => 'income'],
                ['name' => 'Makanan & Minuman', 'type' => 'expense'],
                ['name' => 'Transportasi', 'type' => 'expense'],
                ['name' => 'Tagihan & Utilitas', 'type' => 'expense'],
                ['name' => 'Belanja & Hiburan', 'type' => 'expense'],
                ['name' => 'Kesehatan & Medis', 'type' => 'expense'],
                ['name' => 'Pendidikan & Keluarga', 'type' => 'expense'],
                ['name' => 'Cicilan & Hutang', 'type' => 'expense'],
                ['name' => 'Lain-lain (Pengeluaran)', 'type' => 'expense'],
            ];

            foreach ($defaultCategories as $cat) {
                self::create([
                    'user_id' => $userId,
                    'name' => $cat['name'],
                    'type' => $cat['type'],
                ]);
            }
        }
    }
}
