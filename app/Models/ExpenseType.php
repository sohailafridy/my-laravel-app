<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    use HasFactory;

    protected $table = 'expense_type';

    protected $primaryKey = 'exp_type_id';

    protected $guarded = [];

    const UPDATED_AT = null;
}
