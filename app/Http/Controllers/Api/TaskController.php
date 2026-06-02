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
        // return request()->all();
        // return request('perPage');
        // crea un builder para task select * from task;
        $tasks = Task::query();

        // $tasks = $tasks->with('user');

        // // aplicar filtros
        // if(request('filters')){
        //     $filters = request('filters');  
        //     foreach($filters as $field => $conditions){
        //         foreach($conditions as $operator => $value){
                    
        //              if(in_array($operator, ['=', '>', '<', '>=', '<=', '!=', 'like'] )){
        //                 $tasks->where($field, $operator, $value);
        //              }

        //              if($operator == 'like'){
        //                 $tasks->where($field, 'like', "%$value%");
        //              }
        //         }
        //     }
    
        // }

        // aplicar selects
        
        // if(request('select')){
        //     // return request('select');
        //     $select = request('select');
        //     $selectArray = explode(',', $select);
        //     $tasks->select($selectArray);
        // }

        // aplicar orden
        // if(request('sort')){
        //     $sortFields = explode(',', request('sort'));

        //     foreach($sortFields as $sortField){
        //         $direction = 'asc';
        //         if(substr($sortField, 0, 1) == '-'){
        //             $direction = 'asc';
        //             $sortField = substr($sortField, 1);
        //         }

        //         $tasks->orderBy($sortField, $direction); 
        //     }

        //     // return $sortFields;
        // }

        // incluir relaciones
        // if(request('include')){
        //     $include = explode(',', request('include'));
        //     $tasks = $tasks->with($include);
        // }
        // crear consulta
        if(request('perPage')) {
            $tasks = $tasks->paginate(request('perPage'));
        } else {
            $tasks = $tasks->get();
        }

        // $tasks = Task::paginate(5);
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
