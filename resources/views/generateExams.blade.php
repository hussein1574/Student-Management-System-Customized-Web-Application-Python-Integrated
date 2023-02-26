@extends(backpack_view('blank')) @section('content')
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.css">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.js"></script>

<form method="post" action="{{ route('run-script') }}">
    @CSRF
    @method('POST')
    {{-- <input
        type="hidden"
        name="_token"
        value="nZBesZzauQ85WgrrL37JhGuLx1bIj0oWkQ0Q1T6L"
    /> --}}

    {{-- <input
        type="hidden"
        name="_http_referrer"
        value="http://127.0.0.1:8000/admin/run-script"
    /> --}}

    <div class="card">
        <div class="card-body row">
            <div
                class="form-group col-sm-12 required"
                element="div"
                bp-field-wrapper="true"
                bp-field-name="name"
                bp-field-type="text"
            >
                <label>Maximum students per day</label>

                <input type="text" name="maxStds" value="" class="form-control" />
            </div>
            <div
                class="form-group col-sm-12 required"
                element="div"
                bp-field-wrapper="true"
                bp-field-name="grade"
                bp-field-type="text"
            >
                <label>Maximum halls per day</label>
                <input type="text" name="maxRooms" value="" class="form-control" />
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
    <script>
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
    </script>
@endsection

