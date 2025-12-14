<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\CategoryField;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Faculty Needs',
                'description' => 'Documents related to faculty requirements and requests',
                'fields' => [
                    ['name' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'Author', 'type' => 'text', 'required' => true],
                    ['name' => 'Title', 'type' => 'text', 'required' => true],
                    ['name' => 'Description', 'type' => 'textarea', 'required' => false],
                ]
            ],
            [
                'name' => 'Development Plan',
                'description' => 'Faculty and program development plans',
                'fields' => [
                    ['name' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'Author', 'type' => 'text', 'required' => true],
                    ['name' => 'Title', 'type' => 'text', 'required' => true],
                    ['name' => 'Academic Year', 'type' => 'text', 'required' => false],
                ]
            ],
            [
                'name' => 'AACCUP Results',
                'description' => 'AACCUP accreditation results and reports',
                'fields' => [
                    ['name' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'Author', 'type' => 'text', 'required' => true],
                    ['name' => 'Title', 'type' => 'text', 'required' => true],
                    ['name' => 'Accreditation Level', 'type' => 'text', 'required' => false],
                    ['name' => 'Assessment Period', 'type' => 'text', 'required' => false],
                ]
            ],
            [
                'name' => 'COPC Docs',
                'description' => 'COPC (Curriculum, Outcomes, and Performance Criteria) documents',
                'fields' => [
                    ['name' => 'Date', 'type' => 'date', 'required' => true],
                    ['name' => 'Author', 'type' => 'text', 'required' => true],
                    ['name' => 'Title', 'type' => 'text', 'required' => true],
                    ['name' => 'Document Type', 'type' => 'text', 'required' => false],
                    ['name' => 'Notes', 'type' => 'textarea', 'required' => false],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            // Create category
            $category = Category::create([
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
            ]);

            // Create fields for this category
            foreach ($categoryData['fields'] as $field) {
                CategoryField::create([
                    'category_id' => $category->id,
                    'field_name' => $field['name'],
                    'field_slug' => Str::slug($field['name']),
                    'field_type' => $field['type'],
                    'is_required' => $field['required'],
                ]);
            }
        }

        $this->command->info('Categories and fields seeded successfully!');
    }
}