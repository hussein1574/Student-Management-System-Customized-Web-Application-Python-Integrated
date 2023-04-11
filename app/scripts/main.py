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
#maxStds=600, maxRooms= 10, GapDays= 0
# maxStds = int(sys.argv[1])
# maxRooms = int(sys.argv[2])
# #gapDays = int(sys.argv[3])


# Get the absolute path of the script file
script_path = os.path.abspath(__file__)

# Get the directory of the script file
script_dir = os.path.dirname(script_path)

# Join the script directory with the filename
filename = os.path.join(script_dir, 'RealData.xlsx')

# Create an instance of ExamTimetableScript with the file
geneticAlgorithm = ets.ExamTimetableScript(filename,script_dir)
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
        f = open("fitness.txt", "w")
        f.write("Soft Value: "+str(softValue)+"\n")
        f.write("Hard Value: "+str(hardValue))
        f.close()
        break


