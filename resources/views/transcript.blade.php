<?php
use App\Http\Controllers\StudentCoursesController;
 ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Transscript</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js "></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      body {
        font-family: sans-serif;
        font-size: 16px;
        display: grid;
        grid-template-columns: 1fr 1fr 0.5fr;
        grid-template-rows: repeat(6, 1fr);
        height: 100vh;
        padding: 12px;
        column-gap: 24px;
        row-gap: 24px;
      }
      .logo {
        border-radius: 12px;
        padding: 0px 12px;
        background-color : rgba(102, 102, 102, 0.5);
        grid-column: 2 / 4;
        justify-self: end;
      }
      .logo img {
        width: 400px;
      }
      .transcript-body {
        grid-column: 1 / 3;
        text-align: center;
        display: flex;
        align-items: flex-start;
        justify-content: center;
      }
      table {
        width: 90%;
        border-collapse: collapse;
        border: 1px solid black;
      }

      /* th:first-child {
        width: 50%;
      } */

      th,
      td {
        border-right: 1px solid black;
        padding: 2px 0px;
      }
      .year {
        border-top: 1px solid black;
        text-align: start;
        font-weight: bold;
      }
      .term {
        border-top: 0.1px dotted #464646;
        text-align: center;
      }
      .summary {
        grid-column: 3 / 4;
        border: 1px solid black;
        border-top: 0px;
        border-collapse: collapse;
      }
      ul {
        padding: 12px;
        list-style: none;
        line-height: 1.5;
      }

      section {
        margin-bottom: 24px;
      }
      h3 {
        margin-bottom: 12px;
        border-top: 1px solid black;
        border-bottom: 1px solid black;
        text-align: center;
      }
      li {
        display: flex;
        justify-content: flex-start;
        gap: 20px;
      }
    </style>
  </head>
  <body>
    <div class="logo">
      <img
        src="../../enghelwan-logo.png"
        alt="Engineering Helwan – جامعة حلوان – كليـــة الهندسـة بحلـوان"
      />
    </div>
    <div class="transcript-body">
      <table>
        <thead>
          <tr>
            <th>Acadmic Year</th>
            <th>Term</th>
            <th>Course Name</th>
            <th>Course Hours</th>
            <th>Course GPA</th>
          </tr>
        </thead>
       
        @foreach($years as $yearName => $year)
          <tbody>
            <tr class="year">
              <td>{{$yearName}}</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            @foreach($year as $termName => $term)
            <tr class="term">
              <td>&nbsp;</td>
              <td>{{$termName}}</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            @foreach($term as $course)
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>{{$course->course->name}}</td>
              <td>{{$course->course->LectureHours + $course->course->classworkHours + $course->course->labHours}}</td>
              <td>{{StudentCoursesController::getGPA($course->grade + $course->class_work_grade + $course->lab_grade)}}</td>
            </tr>
            @endforeach
          </tbody>
          @endforeach
        @endforeach
      </table>
    </div>
    <div class="summary">
      <section>
        <h3>Acadmic Summary</h3>
        <ul>
          <li><span>Name: </span><span>{{$student['name']}}</span></li>
          <li><span>Department: </span><span>{{$student['department']}}</span></li>
          <li><span>Acadmic Year: </span><span>{{$student['year']}}</span></li>
          <li><span>Overall GPA: </span><span>{{$student['gpa']}}</span></li>
          <li><span>Overall Grade: </span><span>{{$student['grade']}}</span></li>
        </ul>
      </section>
      <section>
        <h3>Grading System</h3>
        <ul>
          <li><span>A+:</span><span> 4.00</span></li>
          <li><span>A :</span><span> 4.00</span></li>
          <li><span>A-:</span><span> 3.70</span></li>
          <li><span>B+:</span><span> 3.30</span></li>
          <li><span>B :</span><span> 3.00</span></li>
          <li><span>B-:</span><span> 2.70</span></li>
          <li><span>C+:</span><span> 2.30</span></li>
          <li><span>C :</span><span> 2.00</span></li>
          <li><span>C-:</span><span> 1.70</span></li>
          <li><span>D+:</span><span> 1.30</span></li>
          <li><span>D :</span><span> 1.00</span></li>
          <li><span>F :</span><span> 0.00</span></li>
        </ul>
      </section>
    </div>
  </body>
</html>
<script>
  window.jsPDF = window.jspdf.jsPDF;
  var docPDF = new jsPDF({
    color: 'rgb'
  });
  docPDF.addImage('../../enghelwan-logo.png', 'PNG', 15, 15, 170, 40);
  function print(){
    var elementHTML = document.querySelector("body");
    docPDF.html(elementHTML, {
      callback: function(docPDF) {
        docPDF.save('Transcript.pdf');
      },
      x: 15,
      y: 15,
      width: 170,
      windowWidth: 850
    });
  }
  print();
  //wait till the pdf is downloaded
  setTimeout(function(){
    // return to the previous page
    window.history.back();
  }, 1000);
  
  

</script>
