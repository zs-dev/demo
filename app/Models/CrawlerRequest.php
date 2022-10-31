<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrawlerRequest extends Model
{
    use HasFactory;

    public function pages()
    {
        return $this->hasMany(Page::class, 'crawler_request_id');
    }
}
