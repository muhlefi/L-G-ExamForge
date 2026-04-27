<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'school_name',
        'class_name',
        'teacher_name',
        'subject',
        'topic',
        'school_level',
        'material_scope',
    ];

    public function questionGroups()
    {
        return $this->hasMany(QuestionGroup::class)->orderBy('created_at');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
