<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRpsTopikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rps_topik', function (Blueprint $table) {
            $table->id();
            $table->integer('id_rps');
            $table->integer('id_cp');
            $table->text('sesi');
            $table->text('topik');
            $table->text('type');
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
        Schema::dropIfExists('rps_topik');
    }
}
