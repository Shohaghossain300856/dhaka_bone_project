<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Backend\BonePost;

class DelivaryController extends Controller
{

    public function index(){
        return view('backend.delivary.index');
    }

 public function create()
 {
    $user = auth()->user();
    $bones = BonePost::where('expire_date', '<', now())
        ->when(!$user->hasRole(['super-admin', 'admin']), function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with([
            'latestBid.user',
            'user',
            'details.images'
        ])
        ->get();

    return response()->json([
        'success' => true,
        'data' => $bones
    ], 200);
}

     public function updateStatus(Request $request, $id)
    {
        
        $request->validate([
            'status' => 'required|in:sold,delivered'
        ]);
        $bone = BonePost::findOrFail($id);
        $bone->status = $request->status;
        $bone->save();
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!',
            'data' => $bone
        ]);
    }

}
