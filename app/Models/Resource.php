<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'resource',
        'page_id',
    ];

    /**
     * Defines relationship.
     *
     * @return BelongsTo
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'id');
    }

    /**
     * Gets remaining urls for crawl randomly.
     *
     * @param string $path
     * @param int $pageId
     * @param int $numberOfPages
     *
     * @return array
     */
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
