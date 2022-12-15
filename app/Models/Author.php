<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $table = "authors";
    protected $fillable = [
        "id",
        "name",
        "first_surname",
        "second_surname"

    ];

    public $timestamps = false;


    public function books(){

        return $this->belongsToMany(
             Book::class, //Tabla relationship
            'authors_books', //indicamos la tabla pibote
            'authors_id', //from
            'books_id' //to (a donde establecer la interception)
        );
    }
}
