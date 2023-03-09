@extends(backpack_view('blank')) @section('content')
<div class="row" style="display: flex;justify-content: space-between;align-items: center;margin-top: 0px;">
    <div class="card" style="width: 49%; height: 100%">
        <div class="card-header"><strong>Add Course</strong></div>
        <div class="card-body">
            <form action="{{ route('register-student-course') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="level">Level:</label>
                    <select class="form-control" id="level" name="level">
                        <option value="">All levels</option>
                        <option value="0">Level 0</option>
                        <option value="1">Level 1</option>
                        <option value="2">Level 2</option>
                        <option value="3">Level 3</option>
                        <option value="4">Level 4</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="course">Course:</label>
                    <select class="form-control" id="course" name="course_id">
                        <!-- dynamically populate options based on level selection -->
                        <option value="">Select a course</option>
                        @foreach ($studentCoursesStatus as $course)
                        <option
                            value="{{ $course['courseId'] }}"
                            data-level="{{ $course['level'] }}"
                            class="course-option"
                        >
                            {{ $course['courseName'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="student_id" value="{{ $studentId }}">
                <button
                    onclick="checkCourseSelected()"
                    type="submit"
                    class="btn btn-primary"
                >
                    Add Course
                </button>
                <a href="{{ route('get-student-courses',$studentId) }}" class="btn btn-danger">Cancel</a>
            </form>
        </div>
    </div>
    <div class="card" style="width: 49%; height: 100%">
        <div class="card-header"><strong>Finished Courses</strong></div>
        <div class="card-body">
            <div style="height: 300px; overflow-y: auto">
                <table class="table">
                    <thead
                        style="
                            position: sticky;
                            top: 0;
                            left: 0;
                            background-color: #f1f4f8;
                        "
                    >
                        <tr>
                            <th>Name</th>
                            <th>Hours</th>
                            <th>Level</th>
                            <th>Grade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($finishedCourses as $course)
                        <tr
                            class="{{ $course['status'] == 1 ? 'table-success' : 'table-danger' }}"
                        >
                            <td>{{ $course['name'] }}</td>
                            <td>{{ $course['hours'] }}</td>
                            <td>{{ $course['level'] }}</td>
                            <td>{{ $course['grade'] }}</td>
                            <td>
                                {{ $course['status'] == 1 ? 'Pass' : 'Fail' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row" style="display: flex;justify-content: space-between;align-items: center">
    <div class="card" style="width: 49%; height: 100%">
        <div class="card-header" id ='pre-requiest-title'><strong>Course Prerequisites</strong></div>
        <div class="card-body">
            <div style="height: 250px; width: 100%; overflow-y: auto">
                <table id="prerequisitesTable" class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Condition</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card" style="width: 49%; height: 100%">
        <div class="card-header"><strong>Available Courses</strong></div>
        <div class="card-body">
            <div style="height: 250px; width: 100%; overflow-y: auto">
                <table class="table">
                    <thead
                        style="
                            position: sticky;
                            top: 0;
                            left: 0;
                            background-color: #f1f4f8;
                        "
                    >
                        <tr>
                            <th>Name</th>
                            <th>Hours</th>
                            <th>Level</th>
                            <th>Status</th>
                            <th>Elective</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentCoursesStatus as $course)
                        <tr>
                            <td>{{ $course['courseName'] }}</td>
                            <td>{{ $course['courseHours'] }}</td>
                            <td>{{ $course['level'] }}</td>
                            <td>
                                @if ($course['state'] == 'closed')
                                <span class="badge badge-secondary"
                                    >Closed</span
                                >
                                @elseif ($course['state'] == 'open')
                                <span class="badge badge-success">Open</span>
                                @elseif ($course['state'] == 'need-pre-req')
                                <span class="badge badge-danger"
                                    >Need Pre-requisite</span
                                >
                                @elseif ($course['state'] == 'retake')
                                <span class="badge badge-warning">Retake</span>
                                @elseif ($course['state'] == 'must-take')
                                <span class="badge badge-dark">Must Take</span>
                                @endif
                            </td>
                            <td>
                                @if ($course['elective'] == 1)
                                <span class="badge badge-success">Yes</span>
                                @else
                                <span class="badge badge-danger">No</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
<link
    rel="stylesheet"
    href="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.css"
/>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.js"></script>
<script>
    
    var select = document.getElementById("level");
    var level = select.options[select.selectedIndex].value;
    select.addEventListener("change", filterCourses);
    function filterCourses() {
        var select = document.getElementById("level");
        var level = select.options[select.selectedIndex].value;
        if (level === "") {
            $("#course option.course-option").show();
        } else {
            $("#course option.course-option").hide();
            $(
                '#course option.course-option[data-level="' + level + '"]'
            ).show();
        }
        var select = document.getElementById("course");
        select.selectedIndex = 0;
    }
    function checkCourseSelected() {
        var select = document.getElementById("course");
        var course = select.options[select.selectedIndex].value;
        if (course === "") {
            Swal.fire("Error", "Please select a course", "error");
            event.preventDefault();
        } 
    }
    const coursePreRequists = {!! json_encode($preCourses) !!};
    const prerequisitesTable = document.getElementById("prerequisitesTable");

    function updatePrerequisitesTable(courseId) {
        // Clear previous table content
        prerequisitesTable.querySelector("tbody").innerHTML = "";
        // Update table title
        const courseName = document.getElementById(`course`).options[
            document.getElementById(`course`).selectedIndex
        ].text;
        document.querySelector("#pre-requiest-title").innerHTML = `<strong>${courseName} pre-requisites</strong>`;
        
        // Find the course's prerequisites data based on the courseId
        var coursePre = coursePreRequists.filter(function (item) {
    return item.course_id == courseId;
});
        console.log(coursePre)
        if (coursePre.length > 0) {
            // Loop through the prerequisite courses and add them to the table
            coursePre.forEach(course => {
                const prerequisite = course.coursePre;
                const status = course.passed ? "<span class='badge badge-success'>Must pass</span>" : "<span class='badge badge-warning'>Only attendance</span>";
                const row = `
                    <tr>
                        <td>${prerequisite}</td>
                        <td>${status}</td>
                    </tr>
                `;
                prerequisitesTable.querySelector("tbody").insertAdjacentHTML("beforeend", row);
            });
        } else {
            // Display an error message if the coursePre data is invalid
            const errorRow = `
                <tr>
                    <td colspan="2" style="text-align: center">No prerequisites data found for this course</td>
                </tr>
            `;
            prerequisitesTable.querySelector("tbody").insertAdjacentHTML("beforeend", errorRow);
        }
    }

    // Call the updatePrerequisitesTable function when a course is selected from the dropdown
    const selectedCourseDropdown = document.getElementById("course");
    selectedCourseDropdown.addEventListener("change", (event) => {
        const selectedCourseId = event.target.value;
        updatePrerequisitesTable(selectedCourseId);
    });

</script>

@endsection
