<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Backend\BonePost;
use App\Models\User;
use App\Models\Backend\Bid;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'bonepost_id',
        'user_id',
        'amount',
    ];

    public function bonepost()
    {
        return $this->belongsTo(BonePost::class, 'bonepost_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
