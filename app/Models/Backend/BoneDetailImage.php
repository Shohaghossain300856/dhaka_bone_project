<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class BoneDetailImage extends Model
{
    use HasFactory;
 protected $table = 'bone_detail_images';

    protected $fillable = [
        'bone_detail_id',
        'images',
    ];


    public function detail()
    {
        return $this->belongsTo(BoneDetail::class, 'bone_detail_id');
    }
}
