<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'name',
            'repeat_every',
            'start_date',
            'end_date',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function taskRepetitions(): HasMany
    {
        return $this->hasMany(TaskRepetitions::class, "task_id", "id");
    }

    public function taskInstances(): HasMany
    {
        return $this->hasMany(TaskInstances::class, "task_id", "id");
    }

}
