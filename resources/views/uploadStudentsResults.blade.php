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
                        <select class="form-control" id="course" name="course_id">
                            <!-- dynamically populate options based on level selection -->
                            <option value="">Select a course</option>
                            @foreach ($professorCourses as $course)
                            <option value="{{ $course['id'] }}" class="course-option">
                                {{ $course['name'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="file">Choose File:</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="mycsv" accept=".csv">
                                <label class="custom-file-label" for="file">Select CSV file</label>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display:flex;justify-content:center;align-items:center; margin-top:30px">
                        <div class="text-right">
                            <button onclick="checkCourseSelected()" type="submit" class="btn btn-primary">Upload File</button>
                        </div>
                    </div>
                    <div class="row" style="display:flex;justify-content:center;align-items:center; margin-top:30px">

                        <button type="button" class="btn btn-success" onclick="exportStudentSheet()">Export Student Sheet</button>

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
    function exportStudentSheet() {
        const courseSelect = document.getElementById("course");
        const courseId = courseSelect.options[courseSelect.selectedIndex].value;
        if (courseId === "") {
            Swal.fire("Error", "Please select a course", "error");
            return;
        }

        // build the URL for the export
        const exportUrl = "{{ route('export-students-sheet') }}";
        const params = new URLSearchParams({
            course_id: courseId
            , export_type: 'without_marks'
        });
        const url = exportUrl + '?' + params.toString();

        // open the export URL in a new window
        window.open(url, '_blank');
    }

    function checkCourseSelected() {
        var select = document.getElementById("course");
        var course = select.options[select.selectedIndex].value;
        if (course === "") {
            Swal.fire("Error", "Please select a course", "error");
            event.preventDefault();
        }
    }
    $(document).ready(function() {
        // Display file name on input change
        $('#file').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });

        $('form').submit(function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch($(this).attr('action'), {
                    method: $(this).attr('method')
                    , body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Please upload a CSV file');
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire('The file is being processed in the background.', data.message, 'success');
                })
                .catch(error => {
                    Swal.fire('Error', error.message, 'error');
                });
        });

    });

</script>
@endsection
