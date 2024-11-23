<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\PostBookRequest;
use App\Http\Resources\BookResource;
use App\Jobs\RetreiveBookContentsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;

class BooksController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        // @TODO implement
        $query = Book::with(['authors', 'bookContents', 'reviews']);

        // Sorting
        if ($request->has('sortColumn') && in_array($request->sortColumn, ['title', 'avg_review', 'published_year'])) {
            $sortColumn = $request->sortColumn;
            $sortDirection = $request->sortDirection === 'DESC' ? 'desc' : 'asc';
            if ($sortColumn === 'avg_review') {
                $query->leftJoin('book_reviews', 'books.id', '=', 'book_reviews.book_id')
                    ->select('books.*', \DB::raw('AVG(book_reviews.review) as avg_review'))
                    ->groupBy('books.id') // Group by book ID
                    ->orderByRaw("avg_review $sortDirection");
            }else {
                $query->orderBy($sortColumn, $sortDirection);
            }
        }

        // Searching by title
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Searching by author ID
        if ($request->has('authors')) {
            $authorIds = explode(',', $request->authors);
            $query->whereHas('authors', function($query) use ($authorIds) {
                $query->whereIn('id', $authorIds);
            });
        }

        // Pagination
        $books = $query->paginate(15);

        return BookResource::collection($books);
    }

    public function store(PostBookRequest $request)
    {
        // @TODO implement
        //Create the book
        $validatedData = $request->validated();
        $book = Book::create($validatedData);

        $book->authors()->attach($request->authors);

        // Dispatch the job to fetch book contents asynchronously
        Queue::push(new RetreiveBookContentsJob($book));

        return new BookResource($book->load(['authors']));
    }
}
