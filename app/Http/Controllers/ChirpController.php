<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChirpRequest;
use App\Http\Requests\UpdateChirpRequest;
use App\Models\Chirp;
use App\Models\Comment;
use Illuminate\Http\Request;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $chirps = Chirp::all();

        // ['chirps' => $chirps] == compact('chirps')
        return view('dashboard', compact('chirps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('chirps.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChirpRequest $request)
    {
        //
        Chirp::create($request->validated());
        return redirect()->route('dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $chirp = Chirp::with('comments')->where('id', $id)->first();
        return view('chirps.show', compact('chirp'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $chirp = Chirp::find($id);
        return view('chirps.edit', compact('chirp'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChirpRequest $request, $id)
    {
        //
        $chirp = Chirp::find($id);

        $chirp->update($request->validated());

        $chirp->save();

        return redirect()->route('dashboard');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $chirp = Chirp::find($id);
        $chirp->delete();
        return redirect()->route('dashboard');
    }


    public function comment(Request $request, $id)
    {

        $request['user_id'] = $request->user()->id;
        $request['chirp_id'] = $id;
        Comment::create($request->all());

        return redirect()->route('chirps.show', ['id' => $id]);


    }
}
