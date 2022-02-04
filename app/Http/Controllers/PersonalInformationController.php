<?php

namespace App\Http\Controllers;

use App\Models\PersonalInformation;
use App\Http\Requests\StorePersonalInformationRequest;
use App\Http\Requests\UpdatePersonalInformationRequest;

class PersonalInformationController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePersonalInformationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePersonalInformationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PersonalInformation  $personalInformation
     * @return \Illuminate\Http\Response
     */
    public function show(PersonalInformation $personalInformation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePersonalInformationRequest  $request
     * @param  \App\Models\PersonalInformation  $personalInformation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePersonalInformationRequest $request, PersonalInformation $personalInformation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PersonalInformation  $personalInformation
     * @return \Illuminate\Http\Response
     */
    public function destroy(PersonalInformation $personalInformation)
    {
        //
    }
}
