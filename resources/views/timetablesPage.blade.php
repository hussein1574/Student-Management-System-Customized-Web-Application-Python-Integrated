@extends(backpack_view('blank')) 

@section('content')
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      .body {
        font-size: 16px;
        font-family: sans-serif;
        display: grid;
        grid-template-columns: 3fr 1fr;
        grid-template-rows: repeat(2, 1fr);
        padding: 12px;
        column-gap: 24px;
        row-gap: 24px;
      }
      .lecture-time-table {
        grid-column: 1 / 2;
        grid-row: 1 / 2;
      }
      .lectures-buttons {
        grid-column: 2 / 3;
        grid-row: 1 / 2;
      }
      .exams-time-table {
        grid-column: 1 / 2;
        grid-row: 2 / 3;
        height: 100%;
      }
      .exams-buttons {
        grid-column: 2 / 3;
        grid-row: 2 / 3;
      }
      .lecture-buttons,
      .exams-buttons {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
      }
      .lecture-buttons button,
      .exams-buttons button {
        margin: 12px 0px;
        padding: 12px 24px;
        border-radius: 12px;
        border: none;
        background-color: #161c2d;
        color: white;
        font-size: 16px;
        cursor: pointer;
      }
      .problems span {
        display: inline-block
        font-size: 18px;
        font-weight: bold;
        padding: 3px 6px;
        border-radius: 100px;
        background-color: #161c2d;
        color: white;
      }
      .problems {
        font-size: 16px;
        text-align: center;
        background-color: rgba(0,40,100,.12);
        padding: 12px;
        border-radius: 12px;
        box-shadow: 0 0 12px rgba(0,40,100,.12);
      }
    </style>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.css">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11.0.10/dist/sweetalert2.min.js"></script>
@if(Session::has('alert-generate'))
<div class="alert alert-{{ Session::get('alert-generate') }}">
    {{ Session::get('message-generate') }}
</div>
@else
 <div class="body">
    @if (Session::has('alert-lectures'))
        <div class="alert alert-{{ Session::get('alert-lectures') }}">
            {{ Session::get('message-lectures') }}
        </div>
    @else
    <table class="lecture-time-table table table-bordered table-active">
      <thead>
        <tr>
          <th>Day/Period</th>
          @foreach($timeperiods as $timeperiod)
            <th>{{ $timeperiod }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach($days as $day)
        <tr>
          <th>{{$day}}</th>
          @foreach($timeperiods as $timeperiod)
            <td>
                @foreach ($lectureTableData[$timeperiod][$day] as $lecture)
                {{ $lecture['course_name'] }} - {{ $lecture['professor_name'] }} - {{ $lecture['hall_name']}}
                            <br>
                        @endforeach
                    </td>
            @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
      <div class="lecture-buttons">
        @if($lecturesTableAdmited == 0)
        @if($timeTableProblems != "")
        <p class="problems">
            {!! html_entity_decode($timeTableProblems) !!}
        </p>
        @endif
        <form method="post" action="{{ route('admit-timetable') }}">
            @CSRF
            @method('POST')
            <div class="form-group">
                <button type="submit" class="btn btn-success">
                    <span class="la la-save" role="presentation" aria-hidden="true"></span>
                    &nbsp;
                    <span data-value="save_and_back">Admit Lectures</span>
                </button>
            </div>
        </form>
        @endif
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
      </div>
    @endif
    @if (Session::has('alert-exams'))
    <div class="alert alert-{{ Session::get('alert-exams') }}">
        {{ Session::get('message-exams') }}
    </div>
    @else
    <table class="exams-time-table table-bordered table-active ">
      <thead>
        <tr>
          <th>Time/Hall</th>
          @foreach($halls as $hall)
            <th>{{ $hall }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
       @foreach ($examDays as $day)
            <tr>
                <th>{{ $day }}</th>
                @foreach ($halls as $hall)
                    <td>
                        @foreach ($examTableData[$hall][$day] as $exam)
                            {{ $exam['course_name'] }}
                            <br>
                        @endforeach
                    </td>
                @endforeach
            </tr>
        @endforeach
      </tbody>
    </table>
    <div class="exams-buttons">
      @if($examProblems != "")
        <p class="problems">
            {!! html_entity_decode($examProblems) !!}
        </p>
        @endif
        @if($examsTableAdmited == 0)
      <form method="post" action="{{ route('admit-exams') }}">
            @CSRF
            @method('POST')
            <div class="form-group">
                <button type="submit" class="btn btn-success">
                    <span class="la la-save" role="presentation" aria-hidden="true"></span>
                    &nbsp;
                    <span data-value="save_and_back">Admit Exams</span>
                </button>
            </div>
        </form>
        @endif
        <form method="post" action="{{ route('clear-exam-timetable') }}">
            @CSRF
            @method('DELETE')
            <div class="form-group">
                <button type="submit" class="btn btn-danger">
                    <span class="la la-trash" role="presentation" aria-hidden="true"></span>
                    &nbsp;
                    <span data-value="clear-table">Clear Exam Table</span>
                </button>
            </div>
        </form>
    </div>
    @endif
  </div>
@endif
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
                    swal('Success', response.message, 'success');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                }
                , error: function(response) {
                    swal('Error', response.message, 'error');
                }
            });
        });
    });
</script>
@endsection
