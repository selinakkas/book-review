<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Kullanıcının gönderdiği istekten 'title' ve 'filter' parametrelerini alır
        $title = $request->input('title');
        $filter = $request->input('filter', '');

        // Book modelinden başlayan bir sorgu builder oluşturur
        $books = Book::when(
            $title,
            fn($query, $title) => $query->title($title)
        );

        //$filter değişkenine göre sorguyu filtreler veya düzenler
        $books = match ($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_6months' => $books->popularLast6Months(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6months' => $books->highestRatedLast6Months(),
            default => $books->latest()->withAvgRating()->withReviewsCount()
        };  

        //Son olarak, sorguyu belirli bir anahtarla önbelleğe alır ve sonuçları getirir
        $cacheKey = 'books:' . $filter . ':' . $title;
        $books =
        #    cache()->remember(
            #    $cacheKey,
                //3600 sn sonra kaydettiği veriyi ön bellekten siler
            #    3600,
            #    fn() =>
                $books->paginate(10);
        #    );

        //Sonuçları bir görünüme geçirerek döndürür
        #return view("books.index", compact("books"));    
        return view("books.index", ["books"=>$books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $cacheKey = "book:" . $id;

        $book = cache()->remember(
            $cacheKey,
            3600,
            fn() => 
            Book::with([
                "reviews" => fn($query) => $query->latest()
        ])->withAvgRating()->withReviewsCount()->findOrFail($id)
    );

        return view("books.show",["book" => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
