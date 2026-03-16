<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Backend\BonePost;

class ReportsController extends Controller
{
  public function index()
  {
    return view('backend.Reports.boneReports');
  }

    public function getBoneReports()
    {
        $bones = BonePost::with([
            'latestBid.user',
            'user',
            'details.images'
        ])->get();

        return response()->json([
            'success' => true,
            'data' => $bones
        ]);
    }

}
