<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
   // protected $casts = [
   //      'load_time' => 'string',
   //  ];

    protected $fillable = [
        'path',
        'title',
        'status',
        'word_count',
        'crawler_request_id',
        'load_time',
    ];

    public function crawlerRequest()
    {
        return $this->belongsTo(CrawlerRequest::class, 'id');
    }

    public function resources()
    {
        return $this->hasMany(Resource::class, 'page_id');
    }
}