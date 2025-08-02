<?php

namespace App\Models;

use App\Traits\Models\Item\Fastable;
use App\Traits\Models\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Item extends Model
{
    use Fastable, HasFactory, Searchable;

    protected $guarded  = ['id'];


    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }


    public function transactions() {
        return $this->belongsToMany(Transaction::class);
    }
}
