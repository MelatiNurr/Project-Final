<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $guarded = [];

    public function riskScores()
    {
        return $this->hasMany(RiskScore::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function ports()
    {
        return $this->hasMany(Port::class);
    }

    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }
}
