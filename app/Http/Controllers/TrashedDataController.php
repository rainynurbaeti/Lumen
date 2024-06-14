<?php

namespace App\Http\Controllers;

use App\Models\TrashedData;
use Illuminate\Http\Request;

class TrashedDataController extends Controller
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TrashedData  $trashedData
     * @return \Illuminate\Http\Response
     */
    public function show(TrashedData $trashedData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TrashedData  $trashedData
     * @return \Illuminate\Http\Response
     */
    public function edit(TrashedData $trashedData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TrashedData  $trashedData
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TrashedData $trashedData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TrashedData  $trashedData
     * @return \Illuminate\Http\Response
     */
    public function destroy(TrashedData $trashedData)
    {
        //
    }
}
