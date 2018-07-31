<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class StyleguideController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        return response()->json(Project::all());
    }

    public function show($id)
    {
        return response()->json(Project::find($id));
    }

    public function create(Request $request)
    {
        $project = Project::create($request->all());

        return response()->json($project, 201);
    }

    public function update($id, Request $request)
    {
    	// print_r($request->all());
       $project = Project::findOrFail($id);
       $project->update($request->all());

       return response()->json($project, 200);
    }

    public function delete($id)
    {
        Project::findOrFail($id)->delete();

        return response('Deleted Project', 200);
    }

}
