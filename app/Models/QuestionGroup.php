<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionGroup extends Model
{
    protected $fillable = [
        'batch_id',
        'name',
        'type',
        'amount',
        'options_count',
        'cognitive_level',
        'with_explanation',
        'with_image',
        'status',
    ];

    protected $casts = [
        'with_explanation' => 'boolean',
        'with_image' => 'boolean',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('id');
    }

    public function getGroupLabelAttribute()
    {
        $types = [
            'pilgan' => 'Pilihan Ganda',
            'isian'  => 'Isian Singkat',
            'esai'   => 'Esai',
        ];
        return $types[$this->type] ?? $this->type;
    }
}
