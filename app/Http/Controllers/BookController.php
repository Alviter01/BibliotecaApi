<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{

    public function index()
    {
        //$books = Book::all(); //Book::with('authors'); //para traer solo los libros y los id de de category y editorial. NO TRAE AUTHORS
        $books = Book::with('authors', 'category', 'editorial')->get(); //para traer los libros con el authors
        /*return [
            "error" => false,
            "message" => "Successfull query",
            "data" => $books
        ];*/
        return $this->getResponse200($books);
    }

    public function store(Request $request)
    {

        DB::beginTransaction();
        try {

            //trim() -> Elimina espacio en blanco (u otro tipo de caracteres) del inicio y el final de la cadena
            $existIsbn = Book::where('isbn', trim($request->isbn))->exists();
            if (!$existIsbn) {
                $book = new Book();
                $book->isbn = trim($request->isbn);
                $book->title = $request->title;
                $book->description = $request->description;
                $book->publish_date = Carbon::now();
                $book->category_id = $request->category["id"];
                $book->editorial_id = $request->editorial_id;
                $book->save();

                foreach ($request->authors as $item) {
                    $book->authors()->attach($item);
                }
                $bookId = $book->id;
                /*return [
                    "status" => true,
                    "message" => "your book has been created",
                    "data" => [
                        "book_id" => $bookId,
                        "book" => $book
                    ]
                ];*/
                return $this->getResponse201('book', 'created', $book);
            } else {
                /* return [
                    "status" => false,
                    "message" => "The ISBN already exists",
                    "data" => []
                ];*/
                return $this->getResponse500(['The isbn field must be unique']);
            }
            DB::commit(); //save all
        } catch (Exception $e) {
            return $this->getResponse500([]);
            DB::rollBack(); //discard changes
        }



        /*
        DATOS DE POSTMAN = http://localhost:8000/api/book/store

        {
   "isbn": "013615250225",
   "title": "register data with C-E-A",
   "description": "Programming book",
    "category":
        {
         "id":1
        },
    "editorial_id": 1,
    "authors":[
        {
            "id":2
        },
        {
            "id":4
        }
        ]
    }

        */
    }

    //UPDATE
    public function update(Request $request, $id)
    {

        $response = $this->getResponse(); //mandar a llamar la función response
        DB::beginTransaction();
        try {
            $book = Book::find($id);
            if ($book) {
                $isbnOwner = Book::where("isbn", $request->isbn)->first(); //validar que el isb sea el mismo
                if (!$isbnOwner || $isbnOwner->id == $book->id) {
                    $book->isbn = trim($request->isbn);
                    $book->title = $request->title;
                    $book->description = $request->description;
                    $book->publish_date = Carbon::now();
                    $book->category_id = $request->category["id"];
                    $book->editorial_id = $request->editorial_id;
                    //DELETE
                    foreach ($book->authors as $item) {
                        $book->authors()->detach($item->id);
                    }
                    $book->update();
                    //ADD
                    foreach ($request->authors as $item) {
                        $book->authors()->attach($item);
                    }
                    $book = Book::with('category', 'editorial', 'authors')->where('id', $id)->get();


                    $response["error"] = false;
                    $request["message"] = "Your book has been updated!";
                    $response["data"] = $book;
                } else {

                    $request["message"] = "ISBN duplicated";
                }
            } else {

                $request["message"] = "404 not found";
            }

            DB::commit(); //save all
        } catch (Exception $e) {
            DB::rollBack(); //discard changes
        }

        return $response;
    }

    public function show($id)
    {

        $response = $this->getResponse(); //mandar a llamar la función response

        $book = Book::with('authors', 'category', 'editorial')->where("id", $id)->get();
        if ($book) {
            $response["error"] = false;
            $request["message"] = "your data has ben showed!";
            $response["data"] = $book;
        } else {

            $response["message"] = "404 not found";
        }
        return $response;
    }

    public function delete($id)
    {

        DB::beginTransaction();
        try {

            $response = $this->getResponse(); //mandar a llamar la función response
            $book = Book::with('authors', 'category', 'editorial')->find($id);

            if ($book) {
                //DELETE
                foreach ($book->authors as $item) {
                    $book->authors()->detach($item->id);
                }
                $book->delete();
                $response["error"] = false;
                $request["message"] = "your data has been deleted!";
                $response["data"] = $book;
            } else {

                $response["message"] = "404 not found";
            }
        } catch (Exception $e) {
        }
        return $response;
    }

    public function addBookReview(Request $request)
    {
        DB::beginTransaction();
        try {
            $book = new BookReview();
            $book->comment = trim($request->comment);
            $book->user_id = trim(auth()->user()->id);
            $book->book_id = trim($request->book_id);
            $book->save();
            $bookRes = BookReview::find($book->book_id);

            DB::commit();
            return $this->getResponse200($book);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->getResponse500([$e->getMessage()]);
        }
    }

    public function updateBookReview(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $book = BookReview::find($request->id);
            if ($book) {


                if ($book->user_id != auth()->user()->id) {
                    return $this->getResponse403();
                }

                $book->comment = trim($request->comment);
                $book->edited = true;
                $book->user_id = trim(auth()->user()->id);
                //   $book->book_id = trim($request->book_id);
                $book->save();

                DB::commit();
                return $this->getResponse200($book);
            }else{
                return $this->getResponse404();
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->getResponse500([$e->getMessage()]);
        }
    }
}
