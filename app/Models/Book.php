<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Book extends Model
{
    use HasFactory;

    public function reviews(){
        return $this->hasMany(Review::class);
    }

    //Bu fonksiyon, belirli bir başlık içeren kayıtları filtrelemek için kullanılır.
    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where("title","like","%". $title ."%");
    }

    //Bu fonksiyon, modeli popülerlik sırasına göre sıralamak için kullanılır.(most popular reviews )
    //değişkenlerin null değerinde olması optional parameter olduğunu gösteir. you can skip it
    public function scopePopular(Builder $query, $from=null, $to=null): Builder | QueryBuilder
    {
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ])
        ->orderBy ('reviews_count','desc');
        
    }

    //Bu fonksiyon, modeli en yüksek ortalama dereceye göre sıralamak için kullanılır.(rating)
    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ], 'rating')
        ->orderBy("reviews_avg_rating","desc");
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder|QueryBuilder{
        return $query->having('reviews_count', '>=', $minReviews);
    }
    private function dateRangeFilter(Builder $query, $from= null, $to =null)
    {
        if($from && !$to){
            $query->where('created_at', '>=', $from);
        } 
        elseif ( !$from && $to) {
            $query->where('created_at', '<=', $to);
        }
        elseif ($from && $to){
            $query->whereBetween('created_at', [$from, $to]);
        } 
    }
}
