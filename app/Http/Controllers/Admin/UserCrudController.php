<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('email');
        CRUD::column('password');
        CRUD::column('role')->label('Role')->type('closure')->function(function ($entry) {
            if ($entry->student) {
                return 'Student';
            } elseif ($entry->professor) {
                return 'Professor';
            } else {
                return '-';
            }
        });
        CRUD::column('isAdmin')->label('Is Admin')->type('closure')->function(function ($entry) {
            return $entry->isAdmin ? "true": 'false';
        });
        // CRUD::column('isAdmin')->label('Is Admin')->type('closure')->function(function ($entry) {
        //     $isAdmin = $entry->isAdmin;
        //     $class = $isAdmin ? 'badge-success' : 'badge-danger';
        //     $label = $isAdmin ? 'true' : 'false';
        //     return "<span class='badge $class'>$label</span>";
        // });
        
        
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserRequest::class);

        CRUD::field('email');
        CRUD::field('password')->type('password');
        CRUD::field('isAdmin');

        // // This will update the User model before saving
        // CRUD::modifyField('password', [
        //     'processValue' => function ($value) {
        //         return Hash::make($value);
        //     }
        // ]);
    }


    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}