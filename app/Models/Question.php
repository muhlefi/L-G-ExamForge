<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'batch_id',
        'question_group_id',
        'subject',
        'question_text',
        'type',
        'cognitive_level',
        'school_level',
        'topic',
        'options',
        'correct_answer',
        'explanation',
        'image_url',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function group()
    {
        return $this->belongsTo(QuestionGroup::class, 'question_group_id');
    }
}
