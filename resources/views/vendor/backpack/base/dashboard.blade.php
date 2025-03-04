@extends(backpack_view('blank'))

@section('content')
<?php
$userId = backpack_user()->id;
$professor = App\Models\Professor::where("user_id", $userId)->first();
$acadmicAdvisor = App\Models\AcademicAdvisor::where(
    "user_id",
    $userId
)->first();
$registrationStatus = DB::table("constants")
    ->where("name", "Regestration Opened")
    ->first()->value;
?>
@if($registrationStatus == 1)
<div class="alert alert-success">
    <strong>Registration is Opened</strong>
</div>
@else
<div class="alert alert-danger">
    <strong>Registration is Closed</strong>
</div>
@endif
@if(session('success'))
<div class="alert alert-success destroy" onclick="destroy()" style="display:flex;justify-content:space-between;">
    {{ session('success') }}
</div>
@endif
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-bar-chart"></i> <b>Failed Students</b>
            </div>
            <div class="card-body">
                <canvas id="myChart" height="175"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <i class="fa fa-table"></i> <b>Failed Students by Course</b>
            </div>
            <div class="card-body" style="padding:0;max-height: 300px; overflow-y:scroll;">
                <table class="table">
                    <thead style="position:sticky; top:0;left:0;background-color: #f1f4f8;">
                        <tr>
                            <th>Course Name</th>
                            <th>Failed Students</th>
                        </tr>
                    </thead>
                    <tbody id="failed-students-table">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
                <div>
                    <i class="fa fa-bar-chart"></i> <b>Registered Students</b>
                </div>
                <div>
                    @if(!$professor && !$acadmicAdvisor)
                    <form action="{{route('clear-students-registration')}}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-primary" id="btn-registered-students">Clear Students Registration</button>
                    </form>
                    <form action="{{route('change-registration-state')}}" method="POST" class="d-inline">
                        @csrf
                        @method('POST')
                        @if($registrationStatus == 0)
                        <button class="btn btn-sm btn-success" id="btn-registered-students">Open Registration</button>
                        @else
                        <button class="btn btn-sm btn-danger" id="btn-registered-students">Close Registration</button>
                        @endif

                    </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <canvas id="myChart2" height="175"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <i class="fa fa-table"></i><b> Registered Students by Course</b>
            </div>
            <div class="card-body" style="padding:0;max-height: 300px; overflow-y: scroll;">
                <table class="table">
                    <thead style="position:sticky; top:0;left:0;background-color: #f1f4f8;">
                        <tr>
                            <th>Course Name</th>
                            <th>Registered Students</th>
                        </tr>
                    </thead>
                    <tbody id="registered-students-table">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
<script>
    function destroy() {
        $('.destroy').hide();
    }
    $(function() {
        $.ajax({
            url: "{{ route('dashboard.failedStudentsChartData') }}"
            , type: 'get'
            , dataType: 'json'
            , success: function(response) {
                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar'
                    , data: response
                    , options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                    , stepSize: 5
                                }
                            }]
                        }
                    }
                });
                var failedStudentsTableBody = $('#failed-students-table');
                for (var i = 0; i < response.labels.length; i++) {
                    var row = $('<tr>');
                    var courseName = $('<td>').text(response.labels[i]);
                    var failedStudents = $('<td>').text(response.datasets[0].data[i]);
                    row.append(courseName);
                    row.append(failedStudents);
                    failedStudentsTableBody.append(row);
                }

            }
        });
        $.ajax({
            url: "{{ route('dashboard.registeredStudentsChartData') }}"
            , type: 'get'
            , dataType: 'json'
            , success: function(response) {
                var ctx = document.getElementById('myChart2').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar'
                    , data: response
                    , options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                    , stepSize: 5
                                }
                            }]
                        }
                    }
                });
                var registeredStudentsTableBody = $('#registered-students-table');
                for (var i = 0; i < response.labels.length; i++) {
                    var row = $('<tr>');
                    var courseName = $('<td>').text(response.labels[i]);
                    var registeredStudents = $('<td>').text(response.datasets[0].data[i]);
                    row.append(courseName, registeredStudents);
                    registeredStudentsTableBody.append(row);
                }
            }
        });
    });

</script>
@endsection
