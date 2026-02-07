<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContractPerson;

class ContractPersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contactpoersondata=ContractPerson::all();
        return response()->json($contactpoersondata);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $createdata = $request->except('_token');
        ContractPerson::create($createdata);
        return response()->json([
            "message" => 'client information create successfully',
            "data" => $createdata,
        ], 201);


      
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
       

        $updatedata=ContractPerson::findOrFail($id);
        $input= $request->except('_token');
        $updatedata->update($input);
        return response()->json([
            "message" => 'contact person data update successfully',
            "data" => $updatedata,
        ]);
        

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $contactpersondeletedata=ContractPerson::find($id);
        $contactpersondeletedata->delete();

        return response()->json([
            "message"=> 'contact person data delete successfully',
        ]);
    }
}
