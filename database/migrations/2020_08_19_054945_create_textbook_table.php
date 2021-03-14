<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTextbookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('textbook', function (Blueprint $table) {
            $table->id();
            $table->text('judul');
            $table->text('pengarang');
            $table->text('isbn');
            $table->integer('tahun_terbit');
            $table->text('edisi');
            $table->text('penerbit');
            $table->text('kota');
            $table->text('kategori');
            $table->text('cover');
            $table->text('status');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('textbook');
    }
}
