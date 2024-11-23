<?php

namespace App\Jobs;

use App\Book;
use App\BookContent;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class RetreiveBookContentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Book $book,
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // @TODO implement
        $isbn = $this->book->isbn;

        // Call the third-party API to get the book details
        $response = Http::get("https://rak-buku-api.vercel.app/api/books/{$isbn}");


        if ($response->successful()) {
            $contents = $response->json('table_of_contents', []);

            foreach ($contents as $content) {
                BookContent::create([
                    'book_id' => $this->book->id,
                    'label' => $content['label'] ?? null,
                    'title' => $content['title'] ?? 'Unknown Title',
                    'page_number' => $content['page_number'] ?? 1,
                ]);
            }
        } else {
            // If the API returns 404, insert a default book content entry
            BookContent::create([
                'book_id' => $this->book->id,
                'label' => null,
                'title' => 'Cover',
                'page_number' => 1,
            ]);
        }
    }
}
