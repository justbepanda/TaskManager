<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description'
    ];

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    public function delete()
    {
        if ($this->tasks()->count() > 0) {
            throw new \Exception("Cannot delete status that has related tasks.");
        }

        return parent::delete();
    }
}
