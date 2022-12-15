<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class BookReview extends Model
{
    use HasFactory;

    protected $table = "book_reviews";

    protected $fillable = [
        "id",
        "comment",
        "edited",
        "user_id",
        "book_id",
    ];

}
