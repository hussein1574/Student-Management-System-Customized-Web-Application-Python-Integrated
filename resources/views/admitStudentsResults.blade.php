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
                            <tr>
                                <th>Course Name</th>
                                <th>Failure Rate</th>
                                <th>Success Rate</th>
                                <th>Average GPA</th>
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
                                <td>{{ $pendingCourse['avg_gpa']}}</td>
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

                                <td style="display:flex">
                                    <form action="{{route('delete-student-results')}}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="course_id" value="{{ $pendingCourse['course_id'] }}">
                                        <button type="submit" class="btn btn-xs btn-danger"><i class="la la-trash"></i>Drop</button>
                                    </form>
                                    <form action="{{route('improve-grades')}}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $pendingCourse['course_id'] }}">
                                        <div class="input-group">
                                            <input style="width:75px" type="number" min=0 max=4 step=0.1 name="grade_value" class="form-control">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-warning">Add gpa</button>
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
@endif

@endsection

@section('after_scripts')
<script>
</script>
@endsection
