<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'title',
        'status',
        'word_count',
        'crawler_request_id',
        'load_time',
    ];

    public function crawlerRequest(): BelongsTo
    {
        return $this->belongsTo(CrawlerRequest::class, 'id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class, 'page_id');
    }
}
