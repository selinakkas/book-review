<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class Review extends Model
{
    use HasFactory;

    public function book(){
        return $this->belongsTo(Book::class);
    }
}
