@extends(backpack_view('blank')) @section('content')
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.css">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.js"></script>

    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ route('run-timetable-script') }}">
                @CSRF
                @method('POST')
                <div class="form-group">
                    <div class="form-group">
                        <label style="font-size:22px;font-weight:bold">Lecture Timetable Generation</label>
                    </div>
                    <button style="display:flex;justify-content:center;align-items:center" type="submit" class="btn btn-success">
                        <span class="la la-save" role="presentation" aria-hidden="true"></span>
                        &nbsp;
                        <span data-value="save_and_back">Generate</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    

<form method="post" action="{{ route('clear-timetable') }}">
    @CSRF
    @method('DELETE')
    <div class="form-group" style="display:flex;justify-content:flex-end">
        <button type="submit" class="btn btn-danger">
            <span class="la la-trash" role="presentation" aria-hidden="true"></span>
            &nbsp;
            <span data-value="clear-table">Clear Lectures Table</span>
        </button>
    </div>
</form>

@endsection
@section('after_scripts')
<script>
    $(document).ready(function() {
        $('form').submit(function(event) {
            event.preventDefault();
            $.ajax({
                url: $(this).attr('action')
                , type: $(this).attr('method')
                , data: $(this).serialize()
                , dataType: 'json'
                , success: function(response) {
                    console.log(response);
                    swal('Success', response.result, 'success');
                }
                , error: function(response) {
                    swal('Error', response.message, 'error');
                }
            });
        });
    });
</script>
@endsection
