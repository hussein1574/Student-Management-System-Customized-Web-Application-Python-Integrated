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
                                <th>GPA</th>
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
            Swal.fire("Error", "Please select a course", "error");
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
                data.forEach(function(student) {
                    var row = tableBody.insertRow();
                    var idCell = row.insertCell();
                    var nameCell = row.insertCell();
                    var gpaCell = row.insertCell();
                    nameCell.innerHTML = student.name;
                    idCell.innerHTML = student.id;
                    gpaCell.innerHTML = '<input type="number" name="gpa[' + student.id + ']" value="' + student.gpa + '" step="0.01" min="0" max="4" required>';
                });
            }
        });
    }

    // Handle form submit
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
                    Swal.fire("Success", message, "success");
                    window.location.href = "{{ backpack_url('dashboard') }}";

                }
                , error: function() {
                    Swal.fire("Error", "Could not save Grades", "error");
                }
            });
        });
    });

</script>
@endsection
