<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $reviews = $this->reviews;
        $avgReview = round($reviews->avg('review'));
        $countReview = $reviews->count();
        return [
            // @TODO implement
            'id' => $this->id,
            'isbn' => $this->isbn,
            'title' => $this->title,
            'description' => $this->description,
            'published_year' => $this->published_year,
            'authors' => AuthorResource::collection($this->authors),
            'book_contents' => BookContentResource::collection($this->bookContents),
            'price' => $this->price,
            'price_rupiah' => usd_to_rupiah_format($this->price),
            'review' => [
                'avg' => $avgReview ?? 0,
                'count' => $countReview ?? 0,
            ],
        ];
    }
}
