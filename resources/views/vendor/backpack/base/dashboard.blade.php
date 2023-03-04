@extends(backpack_view('blank'))

  @section('content')
  <div class="row">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-bar-chart"></i> Failed Students
      </div>
      <div class="card-body">
        <canvas id="myChart" height="100"></canvas>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <i class="fa fa-table"></i> Failed Students by Course
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
      <div class="card-header">
        <i class="fa fa-bar-chart"></i> Registered Students
      </div>
      <div class="card-body">
        <canvas id="myChart2" height="100"></canvas>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <i class="fa fa-table"></i> Registered Students by Course
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
    $(function() {
      $.ajax({
        url: "{{ route('dashboard.failedStudentsChartData') }}",
        type: 'get',
        dataType: 'json',
        success: function(response) {
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
          type: 'bar',
          data: response,
          options: {
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true,
                  stepSize: 1
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
        url: "{{ route('dashboard.registeredStudentsChartData') }}",
        type: 'get',
        dataType: 'json',
        success: function(response) {
          var ctx = document.getElementById('myChart2').getContext('2d');
          var myChart = new Chart(ctx, {
            type: 'bar',
            data: response,
            options: {
            scales: {
              yAxes: [{
              ticks: {
                beginAtZero: true,
                stepSize: 1
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

