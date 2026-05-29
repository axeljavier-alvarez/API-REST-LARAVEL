<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return $request->all();
        $task =Task::create($request->all());
        return response()->json($task, 201);

    }

    /**
     * MUESTRA LA RESPUESTA EN JSON
     */
   public function show($id)
    {
        $task = Task::findOrFail($id);

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $task)
    {
        // return "Metodo update";

        $task = Task::find($task);
        $task->update($request->all());
        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($task)
    {
        //
    }
}
