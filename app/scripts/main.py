import ExamTimetableScript as ets
import os
import sys

import random
import prettytable
import pandas as pd
import numpy as np
import xlsxwriter
import string
import os



# Get the absolute path of the script file
script_path = os.path.abspath(__file__)

# Get the directory of the script file
script_dir = os.path.dirname(script_path)


# Create an instance of ExamTimetableScript with the file
geneticAlgorithm = ets.ExamTimetableScript(script_dir)
timeTable = geneticAlgorithm.createTimeTable()
while(True):
    newTimeTable = geneticAlgorithm.generate(timeTable)
    softValue,hardValue = geneticAlgorithm.calculateFitness(newTimeTable)
    fitValue= softValue + hardValue
    if(hardValue > 0):
        geneticAlgorithm.noOfDays += 1
        timeTable = geneticAlgorithm.createTimeTable()
    else:
        geneticAlgorithm.writeTimeTableToExcelSheet(newTimeTable)
        softValue,hardValue = geneticAlgorithm.calculateFitness(newTimeTable)
        print("Soft Value: ",softValue)
        print("Hard Value: ",hardValue)
        # write text file with the soft and hard value
        filename = os.path.join(script_dir, 'fitness.txt')
        f = open(filename, "w")
        f.write("<span>Soft Problems:</span> <br>")
        if(softValue != 0):
            if(geneticAlgorithm.exceedMaxNumberOfStudents > 0):
                f.write("There are halls that exceed the maximum number of students. <br>")
            if(geneticAlgorithm.exceedGap > 0):
                f.write("There are students with large gaps <br>")
        else:
            f.write("There are no soft problems. <br>")
        f.write("<span>Hard Problems:</span>  <br>")
        if(hardValue != 0):
            if(geneticAlgorithm.countMissingCourses > 0):
                f.write("There are courses without exams <br>")
            if(geneticAlgorithm.countClashesExams > 0):
                f.write("There are 2 exams for same students in the same time <br>")
        else:
            f.write("There are no hard problems. <br>")
        f.close()
        break


