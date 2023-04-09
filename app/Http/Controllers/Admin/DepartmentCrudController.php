<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DepartmentRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DepartmentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DepartmentCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Department::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/department');
        CRUD::setEntityNameStrings('department', 'departments');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name')->label('Name');
        CRUD::column('min_hours_per_term')->label('Min Hours Per Term');
        CRUD::column('high_gpa')->label('High GPA');
        CRUD::column('low_gpa')->label('Low GPA');
        CRUD::column('max_hours_per_term_for_high_gpa')->label('Max Hours Per Term For High GPA');
        CRUD::column('max_hours_per_term_for_avg_gpa')->label('Max Hours Per Term For Avg GPA');
        CRUD::column('max_hours_per_term_for_low_gpa')->label('Max Hours Per Term For Low GPA');
        CRUD::column('graduation_hours')->label('Graduation Hours');
        CRUD::column('graduation_gpa')->label('Graduation GPA');
        CRUD::column('max_gpa_to_retake_a_course')->label('Max GPA to retake a course');
        CRUD::column('graduation_project_needed_hours')->label('Graduation Project Needed Hours');
        CRUD::column('created_at');
        CRUD::column('updated_at');

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
        CRUD::setValidation(DepartmentRequest::class);

        CRUD::field('name')->label('Name');
        CRUD::field('min_hours_per_term')->label('Min Hours Per Term');
        CRUD::field('high_gpa')->label('High GPA');
        CRUD::field('low_gpa')->label('Low GPA');
        CRUD::field('max_hours_per_term_for_high_gpa')->label('Max Hours Per Term For High GPA');
        CRUD::field('max_hours_per_term_for_avg_gpa')->label('Max Hours Per Term For Avg GPA');
        CRUD::field('max_hours_per_term_for_low_gpa')->label('Max Hours Per Term For Low GPA');
        CRUD::field('graduation_hours')->label('Graduation Hours');
        CRUD::field('graduation_gpa')->label('Graduation GPA');
        CRUD::field('max_gpa_to_retake_a_course')->label('Max GPA to retake a course');
        CRUD::field('graduation_project_needed_hours')->label('Graduation Project Needed Hours');

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