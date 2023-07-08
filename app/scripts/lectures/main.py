import TimetableScript as TS
import os




# Get the absolute path of the script file
script_path = os.path.abspath(__file__)

# Get the directory of the script file
script_dir = os.path.dirname(script_path)


# Create an instance of ExamTimetableScript with the file
geneticAlgorithm = TS.TimetableScript(script_dir)
timeTable = geneticAlgorithm.createTimeTable()

geneticAlgorithm.writeTimeTableToExcelSheet(timeTable,'main.xlsx')

newTimeTable = geneticAlgorithm.generate(timeTable)
softValue,hardValue = geneticAlgorithm.calculateFitness(newTimeTable)
geneticAlgorithm.writeTimeTableToExcelSheet(newTimeTable,'Lecture_Table.xlsx')
print("Soft Value: ",softValue)
print("Hard Value: ",hardValue)
# write text file with the soft and hard value
filename = os.path.join(script_dir, 'fitness.txt')
f = open(filename, "w")
f.write("<span>Soft Problems:</span> <br>")
if(softValue != 0):
    if(geneticAlgorithm.countSection_LabBeforeLecError > 0):
        f.write("There are section/lab before it's lecture. <br>")
    if(geneticAlgorithm.countExceedMaxNumberOfStudentsInHall > 0):
        f.write("There are halls that exceed the maximum number of students. <br>")
    if(geneticAlgorithm.countLecsError > 0):
        f.write("There are 2 lectures for the same course but not following each other. <br>")
else:
    f.write("There are no soft problems. <br>")
f.write("<span>Hard Problems:</span>  <br>")
if(hardValue != 0):
    if(geneticAlgorithm.countClashesSubjects > 0):
        f.write("There are courses that clash. <br>")
    if(geneticAlgorithm.countClashesProfs > 0):
        f.write("There are professors that have 2 lectures at the same time. <br>")
    if(geneticAlgorithm.countProfsTimeError > 0):
        f.write("There are professors that assigned to non prefered time <br>")
    if(geneticAlgorithm.countDepartmentError > 0):
        f.write("There are courses that assigned to halls not in their department <br>")
        f.write(str(geneticAlgorithm.countDepartmentError))
    if(geneticAlgorithm.countSection_LabError > 0):
        f.write("There are section/lab divided in two non following periods  <br>")
else:
    f.write("There are no hard problems. <br>")
f.close()





