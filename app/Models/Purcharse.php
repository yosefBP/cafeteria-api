<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purcharse extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 'total_compra'
    ];

    /* Relacion muchos a muchos */
    public function products(){
        return $this->belongsToMany(Product::class);
    }
}
