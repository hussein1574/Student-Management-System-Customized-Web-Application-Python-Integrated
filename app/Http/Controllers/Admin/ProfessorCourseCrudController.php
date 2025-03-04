<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Library\Widget;
use App\Http\Requests\ProfessorCourseRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ProfessorCourseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProfessorCourseCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ProfessorCourse::class);
        CRUD::setRoute(
            config("backpack.base.route_prefix") . "/professor-course"
        );
        CRUD::setEntityNameStrings("professor course", "professor courses");
        //disable edit button
        $this->crud->denyAccess(["update"]);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column("professor_id")
            ->type("closure")
            ->function(function ($entry) {
                return $entry->professor->user->name;
            });
        CRUD::column("course_id");
        CRUD::column("created_at");
        CRUD::column("updated_at");

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
        CRUD::setValidation(ProfessorCourseRequest::class);
        // Widget::add()->type('script')->content('assets/professor.js');
        // crud field for the relation to get the professor name from the user table using the user id column
        // CRUD::field('professor_id')->type('select')->entity('professor')->attribute('id');
        CRUD::field("professor_id")
            ->type("select")
            ->entity("professor")
            ->attribute("name")
            ->options(function ($query) {
                return $query
                    ->join("users", "professors.user_id", "=", "users.id")
                    ->select("professors.id", "users.name")
                    ->get();
            });

        // CRUD::field('Professor Name')->type('text')->attributes(['disabled' => 'disabled']);

        CRUD::field("course_id");

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
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
