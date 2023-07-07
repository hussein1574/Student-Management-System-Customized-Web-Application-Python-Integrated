@extends(backpack_view('blank'))
@section('content')
<div style="display:flex;flex-direction:column">
    <div class = "row" style="padding:0px 15px">
        <div class="card" style="width: 100%; height: 100%">
            <div class="card-header">Current Courses For <strong>{{$studentName}}</strong></div>
            <div class="card-body">
                <div style="height: 300px;width:100%; overflow-y: auto;">
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courses as $course)
                            <tr>
                                <td>{{ $course['name'] }}</td>
                                <td>{{ $course['hours'] }}</td>
                                <td>{{ $course['level'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection