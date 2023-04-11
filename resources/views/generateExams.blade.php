@extends(backpack_view('blank')) @section('content')
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.css">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.js"></script>

<form method="post" action="{{ route('run-script') }}">
    @CSRF
    @method('POST')


    <div class="card">
        <div class="card-body row">
            <div class="form-group col-sm-12 required">
                <label>Exams Starting Date</label>
                <input type="date" name="examsStartDate" value="" class="form-control" />
            </div>
        </div>
    </div>

    <div class="d-none" id="parentLoadedAssets">[]</div>
    <div id="saveActions" class="form-group">
        <input type="hidden" name="_save_action" value="save_and_back" />
        <div class="btn-group" role="group">
            <button type="submit" class="btn btn-success">
                <span
                    class="la la-save"
                    role="presentation"
                    aria-hidden="true"
                ></span>
                &nbsp;
                <span data-value="save_and_back">Generate</span>
            </button>
        </div>

        <a href="{{ route('generate-exams') }}" class="btn btn-default"
            > &nbsp;Reset</a
        >
    </div>
</form>
@endsection
@section('after_scripts')
    {{-- <script>
        $(document).ready(function () {
            $('form').submit(function (event) {
                event.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        Swal.fire('The table generation is running', response.message, 'success');
                    },
                    error: function (response) {
                        Swal.fire('Error', response.responseJSON.message, 'error');
                    }
                });
            });
        });
    </script> --}}
@endsection

