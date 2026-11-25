<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Technology;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (['Software', 'Investigación', 'Impacto social', 'Emprendimiento'] as $name) {
            Category::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
        foreach (['Laravel', 'Kotlin', 'PostgreSQL', 'Python', 'JavaScript', 'Arduino'] as $name) {
            Technology::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
