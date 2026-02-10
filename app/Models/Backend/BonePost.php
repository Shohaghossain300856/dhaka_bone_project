<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Backend\BoneDetail;

class BonePost extends Model
{
    use HasFactory;
protected $fillable = [
    'title', 
    'user_id', 
    'name', 
    'starting_price', 
    'start_date', 
    'expire_date', 
    'image', 
    'description'
];

public function user()
{
    return $this->belongsTo(User::class);
}
public function details()
{
    return $this->hasMany(BoneDetail::class, 'bonepost_id');
}



}
