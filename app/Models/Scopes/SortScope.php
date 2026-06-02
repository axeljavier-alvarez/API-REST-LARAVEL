<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SortScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
       if(empty(request('sort'))){
            

            // return $sortFields;
            return;
        }


        $sortFields = explode(',', request('sort'));

            foreach($sortFields as $sortField){
                $direction = 'asc';
                if(substr($sortField, 0, 1) == '-'){
                    $direction = 'asc';
                    $sortField = substr($sortField, 1);
                }

                $builder->orderBy($sortField, $direction); 
            }

    }
}
