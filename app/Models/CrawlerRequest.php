<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrawlerRequest extends Model
{
    use HasFactory;

    /**
     * Defines relationship.
     *
     * @return HasMany
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'crawler_request_id');
    }
}
