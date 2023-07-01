@extends(backpack_view('blank'))

@section('content')
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.css">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.js"></script>
@if(Session::has('alert'))
<div class="alert alert-{{ Session::get('alert') }}">
    {{ Session::get('message') }}
</div>
@else
<div class="row">
    <div class="col-md-12" style="display:flex;justify-content:space-between;align-items:center;margin-top:30px;">
        <div class="card" style="width:100%;height:100%">
            <div class="card-header">Pending Results</div>
            <div class="card-body">
                <div style="height: 300px;width:100%; overflow-y: auto;">
                    <table class="table table-striped">
                        <thead style="position:sticky; top:0;left:0;background-color: #f1f4f8;">
                            <tr style="width:auto">
                                <th>Course Name</th>
                                <th>Failure Rate</th>
                                <th>Success Rate</th>
                                <th>Average Grade</th>
                                <th>A+</th>
                                <th>A</th>
                                <th>A-</th>
                                <th>B+</th>
                                <th>B</th>
                                <th>B-</th>
                                <th>C+</th>
                                <th>C</th>
                                <th>C-</th>
                                <th>D+</th>
                                <th>D</th>
                                <th>F</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingCourses as $pendingCourse)
                            <tr>
                                <td>{{ $pendingCourse['course_name'] }}</td>
                                <td>{{ $pendingCourse['failure_rate'] }}%</td>
                                <td>{{ $pendingCourse['success_rate'] }}%</td>
                                <td>{{ $pendingCourse['avg_gpa']}}%</td>
                                <td>{{ $pendingCourse['a_plus_count']}}</td>
                                <td>{{ $pendingCourse['a_count']}}</td>
                                <td>{{ $pendingCourse['a_minus_count']}}</td>
                                <td>{{ $pendingCourse['b_plus_count']}}</td>
                                <td>{{ $pendingCourse['b_count']}}</td>
                                <td>{{ $pendingCourse['b_minus_count']}}</td>
                                <td>{{ $pendingCourse['c_plus_count']}}</td>
                                <td>{{ $pendingCourse['c_count']}}</td>
                                <td>{{ $pendingCourse['c_minus_count']}}</td>
                                <td>{{ $pendingCourse['d_plus_count']}}</td>
                                <td>{{ $pendingCourse['d_count']}}</td>
                                <td>{{ $pendingCourse['f_count']}}</td>

                                <td style="display:flex;">
                                    <form action="{{route('delete-student-results')}}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="course_id" value="{{ $pendingCourse['course_id'] }}">
                                        <button type="submit" class="btn btn-xs btn-danger">Drop</button>
                                    </form>
                                    <form action="{{route('improve-grades')}}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $pendingCourse['course_id'] }}">
                                        <div style="width:75px;display:flex;justify-content:space-between"  class="input-group">     
                                            <input  type="number" min=0 max=100 step=1 name="grade_value" class="form-control">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-warning">Add grades</button>
                                            </div>
                                        </div>
                                    </form>
                                    <form action="{{route('admit-students-results')}}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $pendingCourse['course_id'] }}">
                                        <button type="submit" class="btn btn-success ml-2">Admit</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12" style="display:flex;justify-content:space-between;align-items:center;margin-top:30px;">
        <div class="card" style="width:100%;height:100%">
            <div class="card-header">Choose the course you want to view</div>
            <div class="card-body">
                <form action="{{ route('upload-students-results') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="form-group">
                        <label for="course">Course:</label>
                        <select class="form-control" id="course" name="course_id" onchange="getStudents()">
                            <!-- dynamically populate options based on level selection -->
                            <option value="">Select a course</option>
                            @foreach ($pendingCourses as $course)
                            <option value="{{ $course['course_id'] }}" class="course-option">
                                {{ $course['course_name'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row" id="tables" style="display:none">
    <div class="col-md-12" style="display:flex;justify-content:space-between;align-items:center;margin-top:30px;">
        <div class="card" style="width:100%;height:50%">
            <div class="card-header">1 Degree to pass</div>
            <div class="card-body">
                <div style="height: 300px;width:100%; overflow-y: auto;">
                    <input type="hidden" name="course_id" id="selected-course-id">
                    <table class="table table-striped">
                        <thead style="position:sticky; top:0;left:0;background-color: #f1f4f8;">
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Exam grade</th>
                                <th>ClassWork grade</th>
                                <th>Lab grade</th>
                            </tr>
                        </thead>
                        <tbody id="students-1-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card" style="width:100%;height:50%">
            <div class="card-header">2 Degrees to pass</div>
            <div class="card-body">
                <div style="height: 300px;width:100%; overflow-y: auto;">
                    <table class="table table-striped">
                        <thead style="position:sticky; top:0;left:0;background-color: #f1f4f8;">
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Exam grade</th>
                                <th>ClassWork grade</th>
                                <th>Lab grade</th>
                            </tr>
                        </thead>
                        <tbody id="students-2-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" id="tables2" style="display:none">
    <div class="col-md-12" style="display:flex;justify-content:space-between;align-items:center;margin-top:0px;">
        <div class="card" style="width:100%;height:50%">
            <div class="card-header">3 Degrees to pass</div>
            <div class="card-body">
                <div style="height: 300px;width:100%; overflow-y: auto;">
                    <table class="table table-striped">
                        <thead style="position:sticky; top:0;left:0;background-color: #f1f4f8;">
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Exam grade</th>
                                <th>ClassWork grade</th>
                                <th>Lab grade</th>
                            </tr>
                        </thead>
                        <tbody id="students-3-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card" style="width:100%;height:50%">
            <div class="card-header">4 Degrees to pass</div>
            <div class="card-body">
                <div style="height: 300px;width:100%; overflow-y: auto;">
                    <table class="table table-striped">
                        <thead style="position:sticky; top:0;left:0;background-color: #f1f4f8;">
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Exam grade</th>
                                <th>ClassWork grade</th>
                                <th>Lab grade</th>
                            </tr>
                        </thead>
                        <tbody id="students-4-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('after_scripts')
<script>
    function getStudents() {
            var select = document.getElementById("course");
            var course = select.options[select.selectedIndex].value;
            if (course === "") {
                swal("Error", "Please select a course", "error");
                return;
            }

            var studentsTable = document.getElementById("tables");
            studentsTable.style.display = "block";
            var studentsTable = document.getElementById("tables2");
            studentsTable.style.display = "block";

            var selectedCourseId = document.getElementById("selected-course-id");
            selectedCourseId.value = course;

            // Remove existing rows from the tables
            var table1Body = document.getElementById("students-1-table-body");
            table1Body.innerHTML = "";
            var table2Body = document.getElementById("students-2-table-body");
            table2Body.innerHTML = "";
            var table3Body = document.getElementById("students-3-table-body");
            table3Body.innerHTML = "";
            var table4Body = document.getElementById("students-4-table-body");
            table4Body.innerHTML = "";

            // Fetch the students for the selected course using AJAX
            $.ajax({
                url: "{{ route('get-students-for-course') }}"
                , type: "POST"
                , data: {
                    "_token": "{{ csrf_token() }}"
                    , "course_id": course
                }
                , success: function(data) {
                    // Add the student rows to the table
                    data.forEach(function(student) {
                        console.log(student.gpa + student.class_work + student.lab);
                        if(student.gpa + student.class_work + student.lab === 56)
                        {
                            var row = table4Body.insertRow();
                            var idCell = row.insertCell();
                            var nameCell = row.insertCell();
                            var gpaCell = row.insertCell();
                            var classWorkCell = row.insertCell();
                            var labCell = row.insertCell();
                            nameCell.innerHTML = student.name;
                            idCell.innerHTML = student.id;
                            if(student.class_work === null)
                            {
                                student.class_work = 0;
                            }
                            if(student.lab === null)
                            {
                                student.lab = 0;
                            }

                            if(student.class_work === undefined && student.lab === undefined)
                            {
                            gpaCell.innerHTML = student.gpa;
                            classWorkCell.innerHTML = '0';
                            labCell.innerHTML = '0';
                            }
                            else if(student.lab === undefined)
                            {
                            gpaCell.innerHTML = student.gpa;
                            classWorkCell.innerHTML = student.class_work;
                            labCell.innerHTML = '0';
                            }
                            else {
                            gpaCell.innerHTML = student.gpa;
                            classWorkCell.innerHTML = student.class_work;
                            labCell.innerHTML = student.lab;
                            }
                        }
                        if(student.gpa + student.class_work + student.lab === 57)
                        {
                            var row = table3Body.insertRow();
                            var idCell = row.insertCell();
                            var nameCell = row.insertCell();
                            var gpaCell = row.insertCell();
                            var classWorkCell = row.insertCell();
                            var labCell = row.insertCell();
                            nameCell.innerHTML = student.name;
                            idCell.innerHTML = student.id;
                            if(student.class_work === null)
                            {
                                student.class_work = 0;
                            }
                            if(student.lab === null)
                            {
                                student.lab = 0;
                            }

                            if(student.class_work === undefined && student.lab === undefined)
                            {
                            gpaCell.innerHTML = student.gpa;
                            classWorkCell.innerHTML = '0';
                            labCell.innerHTML = '0';
                            }
                            else if(student.lab === undefined)
                            {
                            gpaCell.innerHTML = student.gpa;
                            classWorkCell.innerHTML = student.class_work;
                            labCell.innerHTML = '0';
                            }
                            else {
                            gpaCell.innerHTML = student.gpa;
                            classWorkCell.innerHTML = student.class_work;
                            labCell.innerHTML = student.lab;
                            }
                        }
                        if(student.gpa + student.class_work + student.lab === 58)
                        {
                            var row = table2Body.insertRow();
                            var idCell = row.insertCell();
                            var nameCell = row.insertCell();
                            var gpaCell = row.insertCell();
                            var classWorkCell = row.insertCell();
                            var labCell = row.insertCell();
                            nameCell.innerHTML = student.name;
                            idCell.innerHTML = student.id;
                            if(student.class_work === null)
                            {
                                student.class_work = 0;
                            }
                            if(student.lab === null)
                            {
                                student.lab = 0;
                            }

                            if(student.class_work === undefined && student.lab === undefined)
                            {
                            gpaCell.innerHTML = student.gpa;
                            classWorkCell.innerHTML = '0';
                            labCell.innerHTML = '0';
                            }
                            else if(student.lab === undefined)
                            {
                            gpaCell.innerHTML = student.gpa;
                            classWorkCell.innerHTML = student.class_work;
                            labCell.innerHTML = '0';
                            }
                            else {
                            gpaCell.innerHTML = student.gpa;
                            classWorkCell.innerHTML = student.class_work;
                            labCell.innerHTML = student.lab;
                            }
                        }
                        if(student.gpa + student.class_work + student.lab === 59)
                        {
                            var row = table1Body.insertRow();
                            var idCell = row.insertCell();
                            var nameCell = row.insertCell();
                            var gpaCell = row.insertCell();
                            var classWorkCell = row.insertCell();
                            var labCell = row.insertCell();
                            nameCell.innerHTML = student.name;
                            idCell.innerHTML = student.id;
                            if(student.class_work === null)
                            {
                                student.class_work = 0;
                            }
                            if(student.lab === null)
                            {
                                student.lab = 0;
                            }

                            if(student.class_work === undefined && student.lab === undefined)
                            {
                            gpaCell.innerHTML = student.gpa;
                            classWorkCell.innerHTML = '0';
                            labCell.innerHTML = '0';
                            }
                            else if(student.lab === undefined)
                            {
                            gpaCell.innerHTML = student.gpa;
                            classWorkCell.innerHTML = student.class_work;
                            labCell.innerHTML = '0';
                            }
                            else {
                            gpaCell.innerHTML = student.gpa;
                            classWorkCell.innerHTML = student.class_work;
                            labCell.innerHTML = student.lab;
                            } 
                        }               
                    });
                    if(table1Body.innerHTML === "")
                    {
                        table1Body.innerHTML = "<tr><td colspan='5'>No students found</td></tr>";
                    }
                    if(table2Body.innerHTML === "")
                    {
                        table2Body.innerHTML = "<tr><td colspan='5'>No students found</td></tr>";
                    }
                    if(table3Body.innerHTML === "")
                    {
                        table3Body.innerHTML = "<tr><td colspan='5'>No students found</td></tr>";
                    }
                    if(table4Body.innerHTML === "")
                    {
                        table4Body.innerHTML = "<tr><td colspan='5'>No students found</td></tr>";
                    }
                }
            });
    }
</script>
@endsection
