<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Request;
use App\Models\Author;

class Book extends Model
{
    use HasFactory;

    protected $table = "books";
    protected $fillable = [
        "id",
        "isbn",
        "title",
        "description",
        "publish_date",
        "category_id",
        "editorial_id"
    ];

    public $timestamps = false;

    public function authors()
    {
        return $this->belongsToMany(
            Author::class, //Tabla relationship
            'authors_books', //indicamos la tabla pibote
            'books_id', //from
            'authors_id' //to (a donde establecer la interception)
        );
    }

    //OBTENER LA CATEGORÍA
    public function category()
    {
        /*return $this->belongsTo(Category::class,
         'id', //id tabla (OBLIGATORIA)
         'category_id' //idforanea (OPCIONAL)
        );*/
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

     //OBTENER LA EDITORIAL
     public function editorial()
     {
         /*return $this->belongsTo(Editorial::class,
          'id', //id tabla (OBLIGATORIA)
          'editorial_id' //idforanea (OPCIONAL)
         );*/
         return $this->belongsTo(Editorial::class, 'editorial_id', 'id');

     }

}
