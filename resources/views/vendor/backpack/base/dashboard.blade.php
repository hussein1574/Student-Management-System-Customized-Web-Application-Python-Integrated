@extends(backpack_view('blank'))

@php
    
@endphp

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-bar-chart"></i> Student Course Report
                </div>
                <div class="card-body">
                    <canvas id="myChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <script>
        $(function() {
            $.ajax({
                url: "{{ route('dashboard.chartData') }}",
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
                }
            });
        });
    </script>
@endsection

