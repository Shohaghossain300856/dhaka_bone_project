<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Backend\BonePost;
use App\Models\Backend\Bid;
use Illuminate\Support\Facades\Auth;


class BoneCaseController extends Controller
{
    public function index()
    {
        return view('backend.boneCase.boneCreate');
    }

    public function create()
    {
      
        $data = BonePost::latest()->get();
        
        return response()->json($data, 200);
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'title'          => 'required|string|max:255',
        'name'           => 'required|string|max:255',
        'starting_price' => 'required|numeric',
        'start_date'     => 'required|date',
        'expire_date'    => 'required|date|after_or_equal:start_date',
        'description'    => 'nullable|string',
        'image'          => 'required|image|max:5120',
    ]);

    $validated['user_id'] = auth()->id();
     $validated['status'] = 'active';

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
        $file->move(public_path('storage/bone_posts'), $filename);
        $validated['image'] = 'storage/bone_posts/' . $filename;
    }

    BonePost::create($validated);

    return response()->json(['message' => 'Post created successfully!'], 201);
}


public function bidsCreate(Request $request)
{
    $request->validate([
        'bonepost_id' => 'required|exists:bone_posts,id',
        'bid_amount'  => 'required|numeric|min:1',
    ]);

    $bone = BonePost::findOrFail($request->bonepost_id);

    $highestBid = $bone->bids()->max('amount');
    $highestBid = $highestBid ?? $bone->starting_price;

    if ((float)$request->bid_amount <= (float)$highestBid) {
        return response()->json([
            'message' => 'Bid must be higher than current highest bid',
        ], 422);
    }

    Bid::create([
        'bonepost_id' => $bone->id,  
        'user_id'     => auth()->id(),
        'amount'      => $request->bid_amount,
    ]);

    return response()->json([
        'message' => 'Bid submitted successfully',
    ]);
}

public function update(Request $request, $id)
{
    $post = BonePost::findOrFail($id);

    $validated = $request->validate([
        'title'          => 'required|string|max:255',
        'name'           => 'required|string|max:255',
        'starting_price' => 'required|numeric',
        'start_date'     => 'required|date',
        'expire_date'    => 'required|date|after_or_equal:start_date',
        'description'    => 'nullable|string',
        'image'          => 'nullable|image|max:5120',
    ]);

    if ($request->hasFile('image')) {

        if ($post->image && file_exists(public_path($post->image))) {
            @unlink(public_path($post->image));
        }

        $file = $request->file('image');
        $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
        $file->move(public_path('storage/bone_posts'), $filename);

        $validated['image'] = 'storage/bone_posts/' . $filename;
    }
    unset($validated['user_id']);

    $post->update($validated);

    return response()->json([
        'message' => 'Post updated successfully!',
        'data'    => $post->fresh()
    ], 200);
}


public function destroy($id)
{
    $deletedata = BonePost::findOrFail($id);
     $deletedata->delete();
     
     return response()->json([
        'message' => 'Bone post deleted successfully'
    ], 200);

}
}
