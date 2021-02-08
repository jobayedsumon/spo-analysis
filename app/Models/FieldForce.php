<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldForce extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function order_deliveries()
    {
        return $this->hasMany(OrderDelivery::class, 'SPOCode', 'Code');
    }

    public function retail_visits()
    {
        return $this->hasMany(RetailVisit::class, 'FieldForce', 'Code');
    }

    public function geotags()
    {
        return $this->hasMany(Geotag::class, 'SPOCode', 'Code');
    }

}
