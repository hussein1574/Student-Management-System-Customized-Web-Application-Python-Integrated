@extends(backpack_view('blank'))
@section('content')
<div style="display:flex;flex-direction:column">
    <div class="row">
        <div class="col-md-12" style="display:flex;justify-content:space-between;align-items:center;margin-top:30px;">
            <div class="card" style="width:49%;height:100%">
                <div class="card-header">Pending Courses</div>
                <div class="card-body">
                    <a href="{{ route('show-student-course', $studentId) }}" class="btn btn-primary mb-2">Add Course</a>
                    <div style="height: 300px;width:100%; overflow-y: auto;">
                        <table class="table table-striped">
                            <thead style="position:sticky; top:0;left:0;background-color: #f1f4f8;">
                                <tr>
                                    <th>Name</th>
                                    <th>Hours</th>
                                    <th>Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingCourses as $pendingCourse)
                                <tr>
                                    <td>{{ $pendingCourse->course->name }}</td>
                                    <td>{{ $pendingCourse->course->hours }}</td>
                                    <td>{{ $pendingCourse->course->level }}</td>
                                    <td>
                                        <form action="{{route('delete-student-course')}}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="student_id" value="{{ $studentId }}">
                                            <input type="hidden" name="course_id" value="{{ $pendingCourse->course->id }}">
                                            <button type="submit" class="btn btn-xs btn-danger"><i class="la la-trash"></i>Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row" style="display:flex;justify-content:center;align-items:center; margin-top:30px">
                        <div class="text-right">
                            <form action="{{route('admit-student-courses', $studentId)}}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $studentId }}">
                                <button type="submit" class="btn btn-success">Admit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" style="width:49%;height:100% ">
                <div class="card-header">Available Courses</div>
                <div class="card-body">
                    <div style="height: 400px;width:100%; overflow-y: auto;">
                        <table class="table">
                            <thead style="position:sticky; top:0;left:0;background-color: #f1f4f8;">
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
                                      <span class="badge badge-secondary">Closed</span>
                                    @elseif ($course['state'] == 'open')
                                      <span class="badge badge-success">Open</span>
                                    @elseif ($course['state'] == 'need-pre-req')
                                      <span class="badge badge-danger">Need Pre-requisite</span>
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

    </div>
    <div class = "row" style="padding:0px 15px">
        <div class="card" style="width: 100%; height: 300px">
            <div class="card-header">Finished Courses</div>
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
</div>
@endsection