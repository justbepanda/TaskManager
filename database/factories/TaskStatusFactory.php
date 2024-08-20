<?php

namespace Database\Factories;

use App\Models\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskStatusFactory extends Factory
{
    protected $model = TaskStatus::class;

    /**
     * Определите состояние модели по умолчанию.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
