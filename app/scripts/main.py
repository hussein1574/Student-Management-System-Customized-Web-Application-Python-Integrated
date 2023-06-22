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
        f = open("fitness.txt", "w")
        f.write("Soft Value: "+str(softValue)+"\n")
        f.write("Hard Value: "+str(hardValue))
        f.close()
        break


