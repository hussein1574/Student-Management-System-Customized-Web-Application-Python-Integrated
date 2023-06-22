@extends(backpack_view('blank'))

@section('content')

<div class="row">
    <div class="col-md-12" style="display:flex;justify-content:space-between;align-items:center;margin-top:30px;">
        <div class="card" style="width:100%;height:100%">
            <div class="card-header">Upload the program</div>
            <div class="card-body">
                <form action="{{ route('upload-program') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
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
                            <button type="submit" class="btn btn-success">Upload File</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="card" style="width:100%;height:100%">
    <div class="card-header">Clear Regulation Courses</div>
    <div class="card-body">
        <form method="post" action="{{ route('clear-regulation-courses') }}">
            @CSRF
            @method('DELETE')
            <div class="form-group">
                <label for="course">Regulation:</label>
                <select class="form-control" id="regulation" name="regulation_id">
                    <!-- dynamically populate options based on level selection -->
                    <option value="">Select a regulation</option>
                    @foreach ($regulations as $regulation)
                    <option value="{{ $regulation['id'] }}" class="regulation-option">
                        {{ $regulation['name'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <button type="submit" onclick="checkRegulationSelected()" class="btn btn-danger">
                    <span class="la la-trash" role="presentation" aria-hidden="true"></span>
                    &nbsp;
                    <span data-value="clear-table">Clear Courses</span>
                </button>
            </div>
        </form>
    </div>
</div>


@endsection

@section('after_scripts')
<script>
    function checkRegulationSelected() {
        var select = document.getElementById("regulation");
        var course = select.options[select.selectedIndex].value;
        if (course === "") {
            swal("Error", "Please select a regulation", "error");
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
                    swal('Success', 'The file is being processed in the background.', 'success');
                })
                .catch(error => {
                    swal('Error', 'Uploading file failed please check the file validty', 'error');
                });
        });

    });

</script>
@endsection
