<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

       
         Tag::create([
            'slug' => Str::slug("Web Development"),
            'name' => "Web Development",
        ]);

        Tag::create([
            'slug' => Str::slug("Javascript"),
            'name' => "Javascript",
        ]);

        Tag::create([
            'slug' => Str::slug("Web Design"),
            'name' => "Web Design",
        ]);

    }
}
