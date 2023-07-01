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
f = open("fitness.txt", "w")
f.write("Soft Value: "+str(softValue)+"\n")
f.write("Hard Value: "+str(hardValue))
f.close()




