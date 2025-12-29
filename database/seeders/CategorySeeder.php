<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Conférence'],
            ['name' => 'Atelier'],
            ['name' => 'Formation'],
            ['name' => 'Meetup'],
            ['name' => 'Webinaire'],
            ['name' => 'Team Building'],
            ['name' => 'Networking'],
            ['name' => 'Sportif'],
            ['name' => 'Culturel'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}