<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\InboundStuff;
use App\Models\Stuff;
use Illuminate\Support\Facades\Storage;
use App\Models\StuffStock;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class InboundStuffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            if($request->filter_id) {
                $data = InboundStuff::where('stuff_id',$request->filter_id)->with('stuff');
            } else {
                $data = InboundStuff::with('stuff')->get();
            }
            return ApiFormatter::sendResponse(200, 'berhasil', $data);
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
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
    public function store(Request $request) // proof file disesuaikan
    {
        try {
            $this->validate($request, [
                'stuff_id' => 'required',
                'total' => 'required',
                'date' => 'required',
                'proff_file' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);


            if ($request->hasFile('proff_file')) { // ngecek ada file apa engga
                $proof = $request->file('proff_file'); // get filenya
                $destinationPath = 'proof/'; // sub path di folder public
                //20240308102130
                $proofName = date('YmdHis') . "." . $proof->getClientOriginalExtension(); // modifikasi nama file, tahunbulantanggaljammenitdetik.extension
                $proof->move($destinationPath, $proofName); // file yang sudah di get diatas dipindahkan ke folder public/proof dengan nama sesaui yang di variabel proofname
            }

            $createStock = InboundStuff::create([
                'stuff_id' => $request->stuff_id,
                'total' => $request->total,
                'date' => $request->date,
                'proff_file' => $proofName,
            ]);

            if ($createStock) {
                $getStuff = Stuff::where('id', $request->stuff_id)->first();
                $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();

                if (!$getStuffStock) {
                    $updateStock = StuffStock::create([
                        'stuff_id' => $request->stuff_id,
                        'total_available' => $request->total,
                        'total_defac' => 0,
                    ]);
                } else {
                    $updateStock = $getStuffStock->update([
                        'stuff_id' => $request->stuff_id,
                        'total_available' => $getStuffStock['total_available'] + $request->total,
                        'total_defac' => $getStuffStock['total_defac'],
                    ]);
                }

                if ($updateStock) {
                    $getStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
                    $stuff = [
                        'stuff' => $getStuff,
                        'inboundStuff' => $createStock,
                        'stuffStock' => $getStock,
                    ];

                    return ApiFormatter::sendResponse(200, true, 'Successfully Create A Inbound Stuff Data', $stuff);
                } else {
                    return ApiFormatter::sendResponse(400, false, 'Failed To Update A Stuff Stock Data');
                }
            } else {
                return ApiFormatter::sendResponse(400, false, 'Failed To Create A Inbound Stuff Data');
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InboundStuff  $inboundStuff
     * @return \Illuminate\Http\Response
     */
    public function show(InboundStuff $inboundStuff)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InboundStuff  $inboundStuff
     * @return \Illuminate\Http\Response
     */
    public function edit(InboundStuff $inboundStuff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InboundStuff  $inboundStuff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InboundStuff $inboundStuff)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InboundStuff  $inboundStuff
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $checkProses = Stuff::where('id',$id)->delete();

            return ApiFormatter::sendResponse(200,'success','data berhasil di hapus');
        }catch(\Exception  $err) {
            return ApiFormatter::sendResponse(400,'bad request',$err->getMessage());
        }
    }
    public function restore($id)
    {
        try{

        $checkProses= Stuff::onlyTrashed()->where('id',$id)->restore();

        if ($checkProses) {
             $data = Stuff::find($id);
            return ApiFormatter:: sendResponse(200,'success', $data);
        }else{
            return ApiFormatter::sendResponse(400,'bad request','gagal mengembalikan data');
        }
        }catch (\Exception $err){
            return ApiFormatter::sendResponse(400,'bad request',$err->getMessage());
        }
    }
    public function trash()
    {
        try{
            $data= Stuff::unlink()->get();

            return ApiFormatter::sendResponse(200,'success','data ');
        }catch(\Exception  $err) {
            return ApiFormatter::sendResponse(400,'bad request',$err->getMessage());
        }
    }

    public function deletePermanent($id)
    {
        try{

        $checkProses= InboundStuff::where('id',$id)->first();
        unlink(base_path('public/proof/'.$checkProses->proff_file));
            $checkProses->forceDelete();
            return ApiFormatter:: sendResponse(200,'success', 'Berhasil menghapus permanen data  stuff!');

        }catch (\Exception $err){
            return ApiFormatter::sendResponse(400,'bad request',$err->getMessage());
        }
    }

}
