<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Backend\BoneDetail;
use App\Models\Backend\BoneDetailImage;
use App\Models\Backend\BonePost;


class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home()
    {
        $data['page_title'] = "Dashboard";
        return view('backend.dashboard.welcome',$data);
    }

public function create()
{
    $bones = BonePost::with([
        'latestBid.user',
        'user',
        'details.images'
    ])->get();

    return response()->json([
        'success' => true,
        'data' => $bones
    ], 200);
}

public function boneDetails($id)
{
    $bone = BonePost::with([
        'latestBid.user',
        'bids.user',
        'user',
        'details.images'
    ])->findOrFail($id);

    return view('backend.boneCase.boneDetails', compact('bone'));
}



}
