<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rps', function (Blueprint $table) {
            $table->id();
            $table->integer('id_textbook');
            $table->text('deskripsi_mata_kuliah');
            $table->text('peta_kompetensi');
            $table->text('metode_penilaian');
            $table->text('media_pembelajaran');
            $table->text('rubrik_penilaian');
            $table->text('strategi_pembelajaran');
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
        Schema::dropIfExists('rps');
    }
}
