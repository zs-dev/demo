<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'resource',
        'page_id',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class, 'id');
    }

    public static function getRemainingUrls(string $path, int $pageId, int $numberOfPages): array
    {
        return self::whereNotIn('path', [$path])
                    ->where('resource', 'internal_link')
                    ->where('page_id', $pageId)
                    ->inRandomOrder()
                    ->limit($numberOfPages)
                    ->get(['path'])
                    ->toArray();
    }
}



