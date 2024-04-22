<?php

namespace App\Http\Controllers;

use APP\Helpers\ApiFormatter;
use App\Models\StuffStock;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;

class StuffStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function show(StuffStock $stuffStock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function edit(StuffStock $stuffStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StuffStock $stuffStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(StuffStock $stuffStock)
    {
        //
    }


    public function addStock(Request $request,$id)
    {
        try{
            $getStuffStock = StuffStock::find($id);

            if (!$getStuffStock) {
                return ApiFormatter::sendResponse(404,false,'Data Stuff Stock Not Found');
            }else{
                $this->validate($request,[
                    'total_available'=>'required',
                    'total_defac'=>'required',
                ]);

                $addStock = $getStuffStock->update([
                    'total_available' => $getStuffStock['total_available']+$request->total_available,
                    'total_devac' => $getStuffStock['total_defac']+$request->total_defac,
                ]);

                if ($addStock) {
                    $getStockAdded = StuffStock::where('id',$id)->with('stuff')->first();

                    return ApiFormatter::sendResponse(200,true,'Succesfully Add A Stock of Stuff Stock Data',$getStockAdded);
                }
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function subStock(Request $request,$id){

    try{
        $getStuffStock = StuffStock::find($id);

        if (!$getStuffStock){
            return ApiFormatter::sendResponse(404,false,'Data Stuff Stock Not found');
        }else{
            $this->validate($request,[
                'total_available'=>'required',
                'total_defac'=>'required',
            ]);

            $isStockAvailable = $getStuffStock['total_available']-$request->total_available;
            $isStockAvailable = $getStuffStock['total_defac']-$request->total_defac;

            if ($isStockAvailable < 0 || $isStockDefac < 0){
                return ApiFormatter::sendResponse(400,true,'A Substraction Stock Cant Less Than A Stock  Stored');
            }else{
                $subStock = $getStuffStock->update([
                    'total_available'=> $isStockAvailable,
                    'total_defac'=> $isStockAvailable,
                ]);

                if ($subStock){
                    $getStockSub = StuffStock::where('id' $id)->with('stuff')->first();
                }
            }
    }
}
}
}
