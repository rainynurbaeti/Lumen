<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\Restoration;
use App\Models\Lending;
use App\Models\StuffStock;
use Illuminate\Http\Request;

class RestorationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
    $this->middleware('auth:api');
    }
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
    public function store(Request $request,$lending_id)
    {
        try{
            $this->validate($request,[
                'date_time' => 'required',
                'total_good_stuff' => 'required',
                'total_defec_stuff' => 'required',

            ]);

            $getLending = Lending::where('id',  $lending_id)->first();//get data peminjaman yang sesuai dengan pengembalia

            $totalStuffRestoration = (int)$request->total_good_stuff + (int)$request->total_defec_stuff;//variabel penampung jumlah barang yang akan di kembalikan
            if ((int)$totalStuffRestoration > (int)$getLending['total_stuff']){//pengecekan apakah

                return ApiFormatter::sendResponse(400,'bad request','total barang kembali lebih banyak dari barang dipinjam!');
            }else{

                $createRestoration = Restoration::updateOrCreate([
                    'lending_id' => $request->lending_id,
                ], [
                    'date_time' => $request->date_time,
                    'total_good_stuff' => $request->total_good_stuff,
                    'total_defec_stuff' => $request->total_defec_stuff,
                    'user_id' => auth()->user()->id,
                ]);

                $stuffStock = StuffStock::where('stuff_id',$getLending['stuff_id'])->first();
                $totalAvailableStock = (int)$stuffStock['total_available'] + (int)$request->total_good_stuff;
                $totalDefecStock = (int)$stuffStock['total_defec'] + (int)$request->total_defec_stuff;

                $stuffStock->update([
                    'total_available' => $totalAvailableStock,
                    'total_defec' => $totalDefecStock,

                ]);
                $lendingRestoration = Lending::where('id',$lending_id)->with('user','restoration','restoration.user','stuff','stuff.stuffStock')->first();
                return ApiFormatter::sendResponse(200,'success',$lendingRestoration);

            }
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());

        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Restoration  $restoration
     * @return \Illuminate\Http\Response
     */
    public function show(Restoration $restoration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Restoration  $restoration
     * @return \Illuminate\Http\Response
     */
    public function edit(Restoration $restoration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Restoration  $restoration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Restoration $restoration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Restoration  $restoration
     * @return \Illuminate\Http\Response
     */
    public function destroy(Restoration $restoration)
    {
        //
    }
}
