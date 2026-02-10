<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Backend\BonePost;

class BoneDetail extends Model
{
    use HasFactory;
 protected $table = 'bone_details';

    protected $fillable = [
        'bonepost_id',
        'bone_type',
        'body_side',
        'specimen_condition',
        'preservation_method',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function bone()
    {
        return $this->belongsTo(BonePost::class, 'bonepost_id');
    }

    public function images()
    {
        return $this->hasMany(BoneDetailImage::class, 'bone_detail_id');
    }

}
