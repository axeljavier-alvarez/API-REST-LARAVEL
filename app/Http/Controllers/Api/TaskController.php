<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::paginate();
        return response()->json($tasks);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        // return $request->all();
        $request->validate([
            'body' => 'required',
            'user_id' => ['required', 'exists:users,id']
        ]);
        $task =Task::create($request->all());
        return response()->json($task, 201);

    }

    /**
     * MUESTRA LA RESPUESTA EN JSON
     */
   public function show(Task $task)
    {
        // $task = Task::findOrFail($id);

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        // return "Metodo update";

        // $task = Task::find($task);
        $task->update($request->all());
        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($task)
    {
        // return "Registro Eliminado";
        // $task = Task::find($task);
        // $task->delete();
        // return response()->json(null, 204);
        $task = Task::findOrFail($task);
        $task->delete();
        return response()->json([
            'message' => 'Registro eliminado correctamente'
        ], 200);
    }


}
