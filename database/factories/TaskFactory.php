<?php

namespace Database\Factories;

use App\Models\Label;
use App\Models\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Task;
use App\Models\User;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(2);

        return [
            'name' => rtrim($title, '.'),
            'description' => $this->faker->text(),
            'created_by_id' => $this->faker->randomElement(User::all()),
            'assigned_to_id' => $this->faker->randomElement(User::all()),
            'status_id' => $this->faker->randomElement(TaskStatus::all()),
            'created_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
        ];
    }

    public function withLabels($labelCount = 3)
    {
        return $this->afterCreating(function (Task $task) use ($labelCount) {
            $labels = Label::inRandomOrder()->take($labelCount)->pluck('id')->toArray();
            $task->labels()->attach($labels);
        });
    }
}
