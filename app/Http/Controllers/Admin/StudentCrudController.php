<?php

namespace App\Http\Controllers\Admin;

use App\Models\StudentCourse;
use PhpParser\Node\Stmt\Label;
use App\Http\Requests\StudentRequest;
use App\Http\Controllers\CourseRegistrationController;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class StudentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class StudentCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Student::class);
        CRUD::setRoute(config("backpack.base.route_prefix") . "/student");
        CRUD::setEntityNameStrings("student", "students");
        $userId = backpack_user()->id;
        $professor = \App\Models\Professor::where("user_id", $userId)->first();
        if ($professor) {
            $this->crud->denyAccess([
                "list",
                "show",
                "create",
                "update",
                "delete",
            ]);
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $userId = backpack_user()->id;
        $professor = \App\Models\Professor::where("user_id", $userId)->first();
        if (!$professor) {
            $this->crud->addButtonFromView(
                "line",
                "admit",
                "moderate",
                "beginning"
            );
        }
        $this->crud->addButtonFromView(
            "line",
            "print",
            "moderate",
            "beginning"
        );
        $this->crud->addButtonFromView(
            "line",
            "currentCourses",
            "moderate",
            "beginning"
        );
        CRUD::column("id")->label("Student ID");
        CRUD::column("name")
            ->label("Name")
            ->type("closure")
            ->function(function ($entry) {
                return $entry->user->name;
            });
        CRUD::column("user_id")
            ->label("Email")
            ->type("select")
            ->entity("user")
            ->attribute("email")
            ->options(function ($query) {
                return $query->where("isAdmin", false)->get();
            });
        CRUD::column("department_id");
        CRUD::column("level")
            ->label("Level")
            ->type("closure")
            ->function(function ($entry) {
                $courseRegController = new CourseRegistrationController();
                $level = $courseRegController->getStudentLevel($entry->id);
                switch ($level) {
                    case 0:
                        return "Freshman";
                        break;
                    case 1:
                        return "Sophomore";
                        break;
                    case 2:
                        return "Junior";
                        break;
                    case 3:
                        return "Senior-1";
                        break;
                    case 4:
                        return "Senior-2";
                        break;
                    default:
                        return "Unknown";
                }
            });
        CRUD::column("grade")->label("GPA");
        CRUD::column("finished hours")
            ->label("Finsished hours")
            ->type("closure")
            ->function(function ($entry) {
                $courses = StudentCourse::where("student_id", $entry->id)
                    ->where("status_id", 1)
                    ->get();
                $finishedHours = 0;
                foreach ($courses as $course) {
                    $finishedHours +=
                        $course->course->LectureHours +
                        $course->course->labHours +
                        $course->course->sectionHours;
                }
                return $finishedHours != 0 ? $finishedHours : "Zero";
            });
        CRUD::column("created_at");
        CRUD::column("updated_at");

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
        CRUD::setValidation(StudentRequest::class);

        // CRUD::field('user_id')->type('select')->entity('user')->attribute('email')->options(function ($query) {
        //     return $query->where('isAdmin', false)->get();
        // });

        CRUD::field("user_id")
            ->type("select")
            ->entity("user")
            ->attribute("email")
            ->options(function ($query) {
                return $query
                    ->where("isAdmin", false)
                    ->whereNotIn("id", function ($subquery) {
                        $subquery->select("user_id")->from("students");
                    })
                    ->get();
            });

        CRUD::field("department_id");
        CRUD::field("grade")->label("GPA");

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
        $this->crud->setValidation([
            "name" => 'required|min:5|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            "email" => "required|unique:users,email,",
            "department_id" => "required",
        ]);
        CRUD::field("email")
            ->type("select")
            ->entity("user")
            ->attribute("email")
            ->options(function ($query) {
                return $query->where("id", $this->crud->entry->user_id)->get();
            });
        CRUD::field("department_id");
        CRUD::field("grade");
        CRUD::field("batch");
    }
}
