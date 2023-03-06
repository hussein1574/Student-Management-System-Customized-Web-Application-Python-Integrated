@extends(backpack_view('blank'))

@section('content')
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.js"></script>

    <div class="row">
        <div class="col-md-12" style="display:flex;justify-content:space-between;align-items:center;margin-top:30px;">
            <div class="card" style="width:49%;height:100%">
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
@endsection

@section('after_scripts')
    <script>
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
                    method: $(this).attr('method'),
                    body: formData
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
