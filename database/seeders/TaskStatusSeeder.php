<?php

namespace Database\Seeders;

use App\Models\TaskStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'New'],
            ['name' => 'In Progress'],
            ['name' => 'In Testing'],
            ['name' => 'Completed'],
        ];

        foreach ($statuses as $status) {
            TaskStatus::create($status);
        }
    }
}
