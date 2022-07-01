<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre_producto',
        'referencia',
        'precio',
        'categoria',
        'stock'
    ];

    /* Relacion muchos a muchos */
    public function sales(){
        return $this->belongsToMany(Purcharse::class);
    }
}
