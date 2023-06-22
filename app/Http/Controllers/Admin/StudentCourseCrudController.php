<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StudentCourseRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class StudentCourseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class StudentCourseCrudController extends CrudController
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
        CRUD::setModel(\App\Models\StudentCourse::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/student-course');
        CRUD::setEntityNameStrings('student course', 'student courses');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // get the user name using the user id column

        CRUD::column('student_id')->label('Student ID')->type('closure')->function(function ($entry) {
            return $entry->student->id;
        });
        CRUD::column('name')->label('Name')->type('closure')->function(function ($entry) {
            return $entry->student->user->name;
        });
        CRUD::column('email')->type('closure')->label('Email')->function(function ($entry) {
            // dd($entry->student->user->email);
            return $entry->student->user->email;
        });
        CRUD::column('course_id');
        //CRUD::column('status_id')->type('select')->entity('courseStatus')->attribute('status');
        CRUD::column('grade');
        CRUD::column('class_work_grade');
        CRUD::column('lab_grade');
        CRUD::column('gpa_score')->label('GPA Score')->type('closure')->function(function ($entry) {
            $fullDegree = $entry->grade + $entry->class_work_grade + $entry->lab_grade;
            if($fullDegree >= 97) {
                return 4.0;
            } elseif($fullDegree >= 93 && $fullDegree < 97) {
                return 4.0;
            } elseif($fullDegree >= 89 && $fullDegree < 93) {
                return 3.7;
            } elseif($fullDegree >= 84 && $fullDegree < 89) {
                return 3.3;
            } elseif($fullDegree >= 80 && $fullDegree < 84) {
                return 3.0;
            } elseif($fullDegree >= 76 && $fullDegree < 80)
            {
                return 2.7;
            } elseif($fullDegree >= 73 && $fullDegree < 76)
            {
                return 2.3;
            } elseif($fullDegree >= 70 && $fullDegree < 73)
            {
                return 2.0;
            } elseif($fullDegree >= 67 && $fullDegree < 70)
            {
                return 1.7;
            } elseif($fullDegree >= 64 && $fullDegree < 67)
            {
                return 1.3;
            } elseif($fullDegree >= 60 && $fullDegree < 64)
            {
                return 1.0;
            } else {
                return 0.0;
            }
        });
        CRUD::column('gpa_grade')->label('GPA Grade')->type('closure')->function(function ($entry) {
            $fullDegree = $entry->grade + $entry->class_work_grade + $entry->lab_grade;
             if($fullDegree >= 97) {
                return 'A+';
            } elseif($fullDegree >= 93 && $fullDegree < 97) {
                return 'A';
            } elseif($fullDegree >= 89 && $fullDegree < 93) {
                return 'A-';
            } elseif($fullDegree >= 84 && $fullDegree < 89) {
                return 'B+';
            } elseif($fullDegree >= 80 && $fullDegree < 84) {
                return 'B';
            } elseif($fullDegree >= 76 && $fullDegree < 80)
            {
                return 'B-';
            } elseif($fullDegree >= 73 && $fullDegree < 76)
            {
                return 'C+';
            } elseif($fullDegree >= 70 && $fullDegree < 73)
            {
                return 'C';
            } elseif($fullDegree >= 67 && $fullDegree < 70)
            {
                return 'C-';
            } elseif($fullDegree >= 64 && $fullDegree < 67)
            {
                return 'D+';
            } elseif($fullDegree >= 60 && $fullDegree < 64)
            {
                return 'D';
            } else {
                return 'F';
            }

        });
        
        
        //CRUD::column('created_at');
        //CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }
    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(StudentCourseRequest::class);

        CRUD::field('student_id')->type('select')->entity('student')->attribute('name')->options(function ($query) {
            return ($query->select('students.id as id', 'users.name')->join('users', 'users.id', '=', 'students.user_id')->get()) ;
        });
        // CRUD::field('student_id')->type('select')->entity('student')->attribute('id');
        CRUD::field('course_id');
        CRUD::field('status_id')->type('select')->entity('courseStatus')->attribute('status');
        CRUD::field('grade');
        CRUD::field('class_work_grade');
        CRUD::field('lab_grade');

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
        CRUD::field('student_id')->type('select')->entity('student')->attribute('name')->options(function ($query) {
            return ($query->select('students.id as id', 'users.name')->join('users', 'users.id', '=', 'students.user_id')->get()) ;
        });
        // CRUD::field('student_id')->type('select')->entity('student')->attribute('id');
        CRUD::field('course_id');
        CRUD::field('status_id')->type('select')->entity('courseStatus')->attribute('status');
        CRUD::field('grade');
        CRUD::field('class_work_grade');
        CRUD::field('lab_grade');
    }
}