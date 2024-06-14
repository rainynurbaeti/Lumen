<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\Lending;
use App\Models\Restoration;
use App\Models\StuffStock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LendingController extends Controller
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
        try{
            $getLending = Lending::with('stuff', 'user','restoration')->get();

            return ApiFormatter::sendResponse(200, 'Successfully Get All Lending Data',$getLending);
        }catch(\Exception $e){
            return ApiFormatter::sendResponse(400, $e->getMessage());

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
        $request->validate([
            'stuff_id' => 'required|integer|exists:stuffs,id',
            'date_time' => 'required|date',
            'name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:255',
            'total_stuff' => 'required|integer|min:1',
        ]);


        $lending = Lending::create([
            'stuff_id' => $request->stuff_id,
            'date_time' => $request->date_time,
            'name' => $request->name,
            'notes' => $request->notes ?? '-',
            'total_stuff' => $request->total_stuff,
            'user_id' => auth()->user()->id,
        ]);


        return response()->json(['message' => 'Lending record created successfully', 'data' => $lending], 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $this->validate($request,[
                'stuff_id' => 'required',
                'date_time' => 'required',
                'name' => 'required',
                'total_stuff' => 'required',

            ]);
            //user_id  tidak masuk ke validate karena value nya bukan bersumber dr luar (di pilih user)

            //cek total_available stuff terkait
            $totalAvailable = StuffStock::where('stuff_id',$request->stuff_id)->value('total_available');

            if (is_null($totalAvailable)){
                return ApiFormatter::sendResponse(400,'bad request','Belum ada data inbound!');
            } elseif ((int)$request->total_stuff > (int)$totalAvailable){
                return ApiFormatter::sendResponse(400,'bad  request','stok tidak tersesia!');
            }else{

            $lending = Lending::create([
                'stuff_id' => $request->stuff_id,
                'date_time' => $request->date_time,
                'name' => $request->name,
                'user_id' => $request->user_id,
                'notes' => $request->notes ?  $request->notes : '-',
                'total_stuff' => $request->total_stuff,
                'user_id' =>  auth()->user()->id,
          ]);


          $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();

          $updateStock = $getStuffStock->update([
            'total_available' => $getStuffStock['total_available'] -
            $request->total_stuff,
          ]);

          $totalAvailableNow = (int)$totalAvailable - (int)$request->total_stuff;
          $stuffStock = StuffStock::where('stuff_id', $request->stuff_id)->update(['total_available' => $totalAvailableNow]);

          $dataLending = Lending::where('id',$request['id'])->with('user','stuff','stuff.stuffStock')->first();

          return ApiFormatter::sendResponse(200,'success',$dataLending);
        }
    }catch (\Exception $err) {
        return ApiFormatter::sendResponse(400,'bad request',$err->getMessage());

    }
}





    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $getLending = Lending::where('id',$id)->with('user','restoration','restoration.user','stuff','stuff.stuffStock')->first();

                return ApiFormatter::sendResponse(200, 'success',$getLending);

        }catch(\Exception $e){
            return ApiFormatter::sendResponse(400, 'bad request',$e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function edit(Lending $lending)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */

     public function recycleBin()
    {
        try {

            $lendingDeleted = Lending::onlyTrashed()->get();

            if (!$lendingDeleted) {
                return ApiFormatter::sendResponse(404, false, 'Deletd Data Lending Doesnt Exists');
            } else {
                return ApiFormatter::sendResponse(200, true, 'Successfully Get Delete All Lending Data', $lendingDeleted);
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {

            $getLending = Lending::onlyTrashed()->where('id', $id);

            if (!$getLending) {
                return ApiFormatter::sendResponse(404, false, 'Restored Data Lending Doesnt Exists');
            } else {
                $restoreLending = $getLending->restore();

                if ($restoreLending) {
                    $getRestore = Lending::find($id);
                    $addStock = StuffStock::where('stuff_id', $getRestore['stuff_id'])->first();
                    $updateStock = $addStock->update([
                        'total_available' => $addStock['total_available'] - $getRestore['total_stuff'],
                    ]);

                    return ApiFormatter::sendResponse(200, true, 'Successfully Restore A Deleted Lending Data', $getRestore);
                }
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function forceDestroy($id)
    {
        try {

            $getLending = Lending::onlyTrashed()->where('id', $id);

            if (!$getLending) {
                return ApiFormatter::sendResponse(404, false, 'Data Lending for Permanent Delete Doesnt Exists');
            } else {
                $forceStuff = $getLending->forceDelete();

                if ($forceStuff) {
                    return ApiFormatter::sendResponse(200, true, 'Successfully Permanent Delete A Lending Data');
                }
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try{
            //get daya inbound yang mau di update
            $getLending = Lending::find($id);

            if($getLending){
                $this->validate($request,[
                    'stuff_id' => 'required',
                    'date_time' => 'required',
                    'name' => 'required',
                    'user_id' => 'required',
                    'notes' => 'required',
                    'total_stuff' => 'required',
                ]);



                $getStuffStock = StuffStock::where('stuff_id',$request->stuff_id)->first();
                $getCurrentSock= StuffStock::where('stuff_id', $getLending['stuff_id'])->first();

                if ($request->stuff_id == $getCurrentSock['stuff_id']){
                    $updateStock = $getCurrentSock->update([
                        'total_available' => $getCurrentSock['total_available'] +
                        $getLending['total_stuff'] - $request->total_stuff,
                    ]);
                }else{
                    $updateStock = $getCurrentSock->update([
                        'total_available' => $getCurrentSock['total_available'] +
                         $getLending['total_stuff'],
                    ]);

                    $updateStock = $getStuffStock ->update([
                        'total_available' => $getCurrentSock['total_available'] -
                        $request['total_stuff'],
                    ]);
                }
                $updateLending = $getLending->update([
                    'stuff_id' => $request->stuff_id,
                'date_time' => $request->date_time,
                'name' => $request->name,
                'user_id' => $request->user_id,
                'notes' => $request->notes,
                'total_stuff' => $request->total_stuff,
                ]);

                $getUpdateLending = Lending::where('id',$id)->with('stuff','user','restoration')->first();
                return ApiFormatter::sendResponse(200, 'Successfully Get All Lending Data',$getLending);

            }
        } catch(\Exception $e){
            return ApiFormatter::sendResponse(400,$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */

            public function destroy($id)
            {
                try{
                    $getlending = Lending::find($id);

                    if (!$getlending) {
                        return ApiFormatter::sendResponse(404,false,'data lending dengan id tersebut tidak di temukan');

                    }else if($getlending -> restoration){
                        return ApiFormatter::sendResponse(404,false,'data lending ini memiliki data resturation(pernah di kensel sebelum nya)');

                    }else{
                        $getlending->update([
                            'user_id'=>$getlending->user_id,
                            'lending_id'=>$getlending->id,
                            'date_time'=>Carbon::now(),
                            'total_good_stuff'=>0,
                            'total_good_defec'=>0,

                        ]);

                        StuffStock::where('stuff_id',$getlending->stuff_id)->increment('total_available',$getlending->total_stuff);
                        $getlending->delete();
                        return ApiFormatter::sendResponse(200,true,'succes fullly restore lending data');
                    }
                } catch(\Exception $e){
                    return ApiFormatter::sendResponse(400,false,$e->getMessage());
                }
            }
        }




