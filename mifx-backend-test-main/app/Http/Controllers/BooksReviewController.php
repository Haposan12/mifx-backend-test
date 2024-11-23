<?php

namespace App\Http\Controllers;

use App\Book;
use App\BookReview;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookReviewResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BooksReviewController extends Controller
{
    public function __construct()
    {

    }

    public function store(int $bookId, PostBookReviewRequest $request)
    {
        // @TODO implement
        $validatedData = $request->validated();
        //validate book exist or not
        $book = Book::find($bookId);
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        // Create a new review
        $review = $book->reviews()->create([
            'review' => $validatedData['review'],
            'comment' => $validatedData['comment'],
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'data' => [
                'id' => $review->id,
                'review' => $review->review,
                'comment' => $review->comment,
                'user' => [
                    'id' => $review->user_id,
                    'name' => $review->user->name,
                ]
            ]
        ], 201);
    }

    public function destroy(int $bookId, int $reviewId, Request $request)
    {
        // @TODO implement
        $book = Book::find($bookId);
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }


        // Validate the review exists for this book
        $review = BookReview::where('book_id', $bookId)->find($reviewId);
        if (!$review) {
            return response()->json(['error' => 'Review not found'], 404);
        }

        // Delete the review
        $review->delete();

        // Return a 204 No Content response
        return response()->json([], 204);
    }
}
