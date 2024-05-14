<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = ['user_id', 'game_id', 'snap_token', 'order_id', 'qty'];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }
}

