<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'status_id');
    }

    public function delete()
    {
        if ($this->tasks()->count() > 0) {
            // Генерируем исключение, если есть связанные задачи
            throw new \Exception("Cannot delete status that has related tasks.");
        }

        return parent::delete();
    }
}
