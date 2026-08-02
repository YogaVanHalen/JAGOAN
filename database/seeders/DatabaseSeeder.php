<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Goal;
use App\Models\Wallet;
use App\Models\Debt;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@email.com'],
            [
                'name' => 'Admin JAGOAN',
                'password' => bcrypt('123456'),
            ]
        );
        $user->update([
            'password' => bcrypt('123456')
        ]);

        // Seed Default Categories for Admin User
        Category::ensureDefaultCategoriesExist($user->id);
    }
}

