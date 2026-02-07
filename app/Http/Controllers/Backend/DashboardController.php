<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Backend\Fund;
use App\Models\Backend\Catagory;
use App\Models\Backend\Subcategorie;
use App\Models\Backend\Supplier;
use App\Models\Backend\Product;
use App\Models\Backend\StockOutItem;
use App\Models\Stock;

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
        return response()->json([
            'status'  => true,
            'message' => 'Count data retrieved successfully',
            'data'    => [
                'users'         => User::count(),
                'funds'         => Fund::count(),
                'categories'    => Catagory::count(),
                'subcategories' => Subcategorie::count(),
                'Supplier'      => Supplier::count(),
                'Product'       => Product::count(),
                'totalStockQty' => Stock::sum('qty'),
                'totalDelivaryQty' => StockOutItem::sum('qty'),

            ],
        ]);
    }

}
