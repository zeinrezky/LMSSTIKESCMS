<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextBook extends Model
{
    protected $primaryKey = "id_text_book";

    protected $table = "text_book";

    public $timestamps = false;

    protected $guarded = [];
}
