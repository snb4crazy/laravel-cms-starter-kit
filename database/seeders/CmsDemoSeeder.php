<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CmsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'editor']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole($adminRole);

        $categories = Category::factory()->count(5)->create();

        $categories->each(function (Category $category): void {
            Post::factory()->count(6)->create([
                'category_id' => $category->id,
            ]);
        });

        // Ensure at least one record for each status in the demo.
        Post::factory()->create(['status' => 'draft']);
        Post::factory()->create(['status' => 'published', 'published_at' => now()->subDay()]);
        Post::factory()->create(['status' => 'archived', 'published_at' => now()->subDays(10)]);
    }
}


