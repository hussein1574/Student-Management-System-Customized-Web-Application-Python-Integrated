@extends(backpack_view('blank'))


@section('content')

@if(Session::has('alert'))
<div class="alert alert-{{ Session::get('alert') }}">
    {{ Session::get('message') }}
</div>
@else
<div class="row">
    <div class="col-md-12" style="display:flex;justify-content:space-between;align-items:center;margin-top:30px;">
        <div class="card" style="width:100%;height:100%">
            <div class="card-header">Upload the Results</div>
            <div class="card-body">
                <form action="{{ route('upload-students-results') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="form-group">
                        <label for="course">Course:</label>
                        <select class="form-control" id="course" name="course_id" onchange="getStudents()">
                            <!-- dynamically populate options based on level selection -->
                            <option value="">Select a course</option>
                            @foreach ($professorCourses as $course)
                            <option value="{{ $course['id'] }}" class="course-option">
                                {{ $course['name'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3" style="display:none" id="students-table">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Students of the Selected Course</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('upload-students-results') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="course_id" id="selected-course-id">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Exam grade</th>
                                <th>ClassWork grade</th>
                                <th>Lab grade</th>
                            </tr>
                        </thead>
                        <tbody id="students-table-body">
                        </tbody>
                    </table>
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary" name="save" value="1">Save</button>
                        <button type="submit" class="btn btn-success" name="send" value="1">Send to admin</button>
                    </div>
                </form>
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

        var studentsTable = document.getElementById("students-table");
        studentsTable.style.display = "block";

        var selectedCourseId = document.getElementById("selected-course-id");
        selectedCourseId.value = course;

        // Remove existing rows from the table
        var tableBody = document.getElementById("students-table-body");
        tableBody.innerHTML = "";

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
                console.log(data[0]);
                console.log(data[0].class_work === undefined);
                console.log(data[0].lab === undefined);
                data.forEach(function(student) {
                    var row = tableBody.insertRow();
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
                    gpaCell.innerHTML = '<input type="number" name="gpa[' + student.id + ']" value="' + student.gpa + '" step="1" min="0" max="100" required>';
                    classWorkCell.innerHTML = '<input type="number" name="class_work[' + student.id + ']" value="0" disabled>';
                    labCell.innerHTML = '<input type="number" name="lab[' + student.id + ']" value="0" disabled>';
                    }
                    else if(student.lab === undefined)
                    {
                    gpaCell.innerHTML = '<input type="number" name="gpa[' + student.id + ']" value="' + student.gpa + '" step="1" min="0" max="50" required>';
                    classWorkCell.innerHTML = '<input type="number" name="class_work[' + student.id + ']" value="' + student.class_work + '" step="1" min="0" max="50" required>';
                    labCell.innerHTML = '<input type="number" name="lab[' + student.id + ']" value="0" disabled>';
                    }
                    else {
                    gpaCell.innerHTML = '<input type="number" name="gpa[' + student.id + ']" value="' + student.gpa + '" step="1" min="0" max="50" required>';
                    classWorkCell.innerHTML = '<input type="number" name="class_work[' + student.id + ']" value="' + student.class_work + '" step="1" min="0" max="30" required>';
                    labCell.innerHTML = '<input type="number" name="lab[' + student.id + ']" value="' + student.lab + '" step="1" min="0" max="20" required>';
                    }
                        
                });
            }
        });
    }

    //Handle form submit
    $(document).ready(function() {
        $('form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var buttonPressed = $(document.activeElement).text().trim();
            if (buttonPressed == "Save")
                formData += "&save=" + buttonPressed;
            else
                formData += "&send=" + buttonPressed;

            // Determine the message to show in the Swal alert based on the button pressed
            var message = buttonPressed === "Save" ? "Grades saved successfully" : "Grades sent successfully";
            $.ajax({
                url: $(this).attr('action')
                , type: "POST"
                , data: formData
                , success: function() {
                     swal("Success", message, "success");
                     //wait 2 seconds, then reload the page
                        setTimeout(function() {
                            window.location.href = "{{ backpack_url('dashboard') }}";
                        }, 2000);
                     

                }
                , error: function() {
                    swal("Error", "Could not save Grades", "error");
                }
            });
        });
    });

</script>
@endsection
