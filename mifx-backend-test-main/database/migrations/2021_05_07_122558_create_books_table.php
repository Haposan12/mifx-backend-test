<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('surname');
        });

        Schema::create('books', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('isbn')->unique();
            $table->string('title');
            $table->text('description');
            $table->double('price', 12, 2);
            $table->integer('published_year');
        });

        Schema::create('book_author', function (Blueprint $table) {
            $table->unsignedbigInteger('book_id');
            $table->unsignedbigInteger('author_id');

            // @TODO implement
            // Foreign keys
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');

            // Ensure the pair is unique (a book can't be associated with the same author twice)
            $table->unique(['book_id', 'author_id']);
        });

        Schema::create('book_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('book_id');
            $table->unsignedbigInteger('user_id');
            $table->tinyInteger('review')->unsigned();
            $table->text('comment');

            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('book_contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('book_id');
            $table->string('label')->nullable();
            $table->string('title');
            $table->string('page_number')->nullable();

            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_reviews');
        Schema::dropIfExists('book_author');
        Schema::dropIfExists('books');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('book_contents');
    }
}
