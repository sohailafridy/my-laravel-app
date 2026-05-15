<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSummary extends Model
{
    use HasFactory;

    protected $table = 'product_summary';

    protected $primaryKey = 'product_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $guarded = [];

    public $timestamps = false;
}
