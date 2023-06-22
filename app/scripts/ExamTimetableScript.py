import random
import prettytable
import pandas as pd
import numpy as np
import xlsxwriter
import string
import os


class ExamTimetableScript :
    script_dir = ""
    conflictData = 0
    new_subjects = 0
    noOfDays = 0
    maxStds = 0
    maxRooms = 0
    GapDays = 0
    minStds = 0
    realHalls = 0
    hallNames = 0
    hallCapacity = 0
    subjects = []
         
    def find_conflict(self,subject1, Days):
        for subject2 in Days:
            if(self.conflictData[subject1][self.new_subjects.get_loc(subject2)-1] == 0):
                return False
        return True
    
    def getTheMinNumberOfDays(self):
        filename = os.path.join(self.script_dir, 'conflict_table.xlsx')
        self.conflictData = pd.read_excel(filename)
        self.new_subjects = self.conflictData.columns
        self.subjects = pd.Index(self.new_subjects)
        #remove first element in subjects
        self.subjects = self.subjects[1:]
        Days = []
        #function to calculate the number of non zero elements in each row in conflictData
        fun = lambda x: sum(x!=0)
        #apply the function to each row in conflictData
        conflictNumbers = self.conflictData.apply(fun, axis=1)
        #sort the conflictNumbers in decending order
        conflictNumbers.sort_values(ascending=False, inplace=True)
        #sort the conflictData in decending order of conflictNumbers
        self.conflictData = self.conflictData.reindex(conflictNumbers.index)
        #get the first column values in conflictData and store it in a list
        conflictNames = self.conflictData.iloc[:,0]
        #save the conflictNames in an array
        conflictNames = np.array(conflictNames)

        self.conflictData = pd.read_excel(filename)
        Days.append(conflictNames[0])

        for i in range(1, len(conflictNames)):
                if self.find_conflict(conflictNames[i], Days):
                    Days.append(conflictNames[i])

        self.noOfDays = len(Days)

    def __init__(self,script_dir, maxStds=600, maxRooms= 10, GapDays= 0):
        self.script_dir = script_dir
        self.maxStds = maxStds
        self.GapDays = GapDays
        subjectsIndecies = []
        registeredSubjects = []
        hallsFilename = os.path.join(self.script_dir, 'halls.xlsx')
        self.realHalls = pd.read_excel(hallsFilename)
        self.hallNames = self.realHalls.iloc[:,0].values
        self.hallCapacity = self.realHalls.iloc[:,1].values
        self.maxRooms = len(self.realHalls)
        self.getTheMinNumberOfDays()

    def writeTimeTableToExcelSheet(self,timetable):
        fileName = os.path.join(self.script_dir, 'Exams_Table.xlsx')
        workbook = xlsxwriter.Workbook(fileName)
        # The workbook object is then used to add new
        worksheet = workbook.add_worksheet()
        halls  = ["Day-Time"]
        for field in range(0,self.maxRooms,1):
            halls.append(self.hallNames[field])
        #add the first row (titles)
        for index, room in enumerate(halls):
            worksheet.write(0, index, room)

        for dayIndex,day in enumerate(timetable):
            subjects = []
            for subjectIndex, subject in enumerate(day):
                if(subjectIndex == 0):
                    subjects.append("9AM - 12PM")
                else:
                    if(subject):
                        subjects.append(subject)
                    else:
                        subjects.append("-")
            # add all the rows
            for subjectIndex, subject in enumerate(subjects):
                worksheet.write(dayIndex+1, subjectIndex, subject)
        workbook.close()
            
    def printTimetable(self,timetable):  
        tableCourses = prettytable.PrettyTable()
        halls  = ["Day-Time"]
        for index in range(0,self.maxRooms,1):
            halls.append(self.hallNames[index])
        tableCourses.field_names =  halls

        counter = 0
        for dayIndex,day in enumerate(timetable):
            subjects = []
            for subjectIndex, subject in enumerate(day):
                if(subject):
                    subjects.append(subject)
                else:
                    subjects.append("-")

            tableCourses.add_row(subjects)
            counter += 1
            
        print(tableCourses)


    def countCommonStudentsInDay(self,daySubjects):
        countedSubjects = []
        numberOfStudents = 0
        for firstSubject in daySubjects:
            for secondSubject in daySubjects:
                if(firstSubject == secondSubject or secondSubject in countedSubjects):
                    continue
                if(firstSubject not in countedSubjects):
                    countedSubjects.append(firstSubject)
                numberOfStudents = self.conflictData[firstSubject][self.subjects.get_loc(secondSubject)] 
        return numberOfStudents

    #Hard constraint No. 1
    def checkMissingSubjects(self,timeTable):
        #For fitness
        countMissingSubjects = 0
        missingSubject = 0
    
        for subject in self.subjects.to_list():
            #Checking missing courses
            itemFound = False
            for day in timeTable:
                if(itemFound):
                    break
                for period in day:
                    if(period):
                        if(period == subject):
                            itemFound = True
                            missingSubject = 0
                            break
                        else:
                            missingSubject += 100
                    else:
                        missingSubject += 100
            countMissingSubjects += missingSubject      
        return countMissingSubjects
    #Hard constraint No. 2
    def checkExamClash(self,timeTable):
        #For fitness
        CountClashes = 0

        for day in timeTable:  
            subjectsInDay = []
            for subject in day:
                if(subject):
                    subjectsInDay.append(subject)

            for firstSubject in subjectsInDay:
                for secondSubject in subjectsInDay:
                    if(firstSubject == secondSubject):
                        continue
                    if(self.conflictData[firstSubject][self.subjects.get_loc(secondSubject)] != 0):
                        CountClashes += 50

        return CountClashes
    #soft constraint #1
    def checkExceedGap(self,timeTable):
        #For fitness
        countExceed = 0
        end = False
        for dayIndex,day in enumerate(timeTable):
            if(dayIndex == (len(timeTable)-2) or end == True):
                break
            for subject in day:
                if(subject):
                    FoundSubject = False
                    index = self.GapDays + 1
                    while(not FoundSubject):
                        if(end == True):
                            break
                        for hall in range(1,self.maxRooms+1):
                            if(dayIndex + (index) < len(timeTable)-2):
                                if(timeTable[dayIndex + (index)][hall]):
                                    FoundSubject = True
                                if(hall == self.maxRooms and (not FoundSubject)):
                                    countExceed += 1000
                                    FoundSubject = True
                            else:
                                end = True
                                break   
        return countExceed
    #soft constraint #2
    def checkExceedMaxNumberOfStudentsInHall(self,timeTable):
        countExceed = 0
        for day in timeTable:
            for hallNumber ,subject in enumerate(day):
                if(subject):
                    if(self.conflictData[subject][self.subjects.get_loc(subject)] > self.hallCapacity[hallNumber-1]):
                        countExceed += 50
        return countExceed
                      
    def createTimeTable(self):
        row = []
        timeTable = []
        # Time table random population
        for dayIndex in range(0,self.noOfDays):
            timeTable.append([row])
            for halls in range(0,self.maxRooms):
                randomSubject = random.choice(self.subjects.to_list())
                timeTable[dayIndex].append((randomSubject))
        #Removing Repeating Subjects
        #if subject is present more then once
        for subject in self.subjects.to_list():
            count = 0
            for dayIndex, day in enumerate(timeTable):
                for periodIndex, period in enumerate(day):
                    if(period != None):
                        if(subject == period):
                            count += 1
                            if(count > 1):
                                timeTable[dayIndex][periodIndex] = None               
        return timeTable

    def calculateFitness(self,timeTable):    
        #Count missing courses
        countMissingCourses = self.checkMissingSubjects(timeTable)
        #Count exam clashes
        countClashesExams = self.checkExamClash(timeTable)
        #Count exceed gap
        exceedGap = self.checkExceedGap(timeTable)
        #Count Exceed Max Number Of Students
        exceedMaxNumberOfStudents = self.checkExceedMaxNumberOfStudentsInHall(timeTable)

        soft_value = exceedMaxNumberOfStudents  + exceedGap
        hard_value = countMissingCourses + countClashesExams 
        
        return soft_value, hard_value
    
    def crossover(self,timeTable):
        chromosomes = []    
        totalCrossovers = 0
        for day in timeTable:
            for subject in day:
                if(subject):
                    totalCrossovers += 1
        totalCrossovers -= 3
        
        while(totalCrossovers):
            #tempChromosome
            tempChromosome = []
            for day in timeTable:
                tempDay = []
                for subject in day:
                    tempDay.append(subject)
                tempChromosome.append(tempDay)

            #Get Location 1
            randomLocation1 = random.randint(0, len(tempChromosome)-1)
            randomSlot1 = random.randint(1, len(tempChromosome[randomLocation1])-1)
            while(tempChromosome[randomLocation1][randomSlot1] == None):
                randomLocation1 = random.randint(0, len(tempChromosome)-1)
                randomSlot1 = random.randint(1, len(tempChromosome[randomLocation1])-1)


            #Get Location 2
            randomLocation2 = random.randint(0, len(tempChromosome)-1)
            randomSlot2 = random.randint(1, len(tempChromosome[randomLocation2])-1)
            while(randomLocation1 == randomLocation2 and randomSlot1 == randomSlot2):
                randomLocation2 = random.randint(0, len(tempChromosome)-1)
                randomSlot2 = random.randint(1, len(tempChromosome[randomLocation2])-1)

            #Interchanging bit
            temp = tempChromosome[randomLocation1][randomSlot1]
            tempChromosome[randomLocation1][randomSlot1] = tempChromosome[randomLocation2][randomSlot2]
            tempChromosome[randomLocation2][randomSlot2] = temp

            #Add new chromosome
            chromosomes.append(tempChromosome)
            
            totalCrossovers -= 1
        
        return chromosomes

    def mutation(self,timeTable): 
        chromosomes = []    
        totalMutations = 0
        for day in timeTable:
            for subject in day:
                if(subject):
                    totalMutations += 1
        totalMutations -= 3
        
        while(totalMutations):
            #tempChromosome
            tempChromosome = []
            for day in timeTable:
                tempDay = []
                for subject in day:
                    tempDay.append(subject)  
                tempChromosome.append(tempDay)

            #Get Random Location
            randomLocation1 = random.randint(0, len(tempChromosome)-1)
            randomSlot1 = random.randint(1, len(tempChromosome[randomLocation1])-1)
    
            
            #Changing Subject
                
            if(tempChromosome[randomLocation1][randomSlot1]):
                currentSubject = tempChromosome[randomLocation1][randomSlot1]
                
                randomSubject = random.choice(self.subjects.to_list())
                while(randomSubject == currentSubject):
                    randomSubject = random.choice(self.subjects.to_list())

                # check if randomSubject exists --> (exists == true) --> replace
                for dayIndex,day in enumerate(tempChromosome):
                    for subjectIndex,subject in enumerate(day):
                        if(subject):
                            if(randomSubject == subject):
                                tempChromosome[dayIndex][subjectIndex] =  currentSubject

                tempChromosome[randomLocation1][randomSlot1] = randomSubject
            else:
                randomSubject = random.choice(self.subjects.to_list())
                # search randomSubject --> remove
                for dayIndex,day in enumerate(tempChromosome):
                    for subjectIndex,subject in enumerate(day):
                        if(subject):
                            if(randomSubject == subject):
                                tempChromosome[dayIndex][subjectIndex] =  0
                tempChromosome[randomLocation1][randomSlot1] = randomSubject

            #Add new chromosome
            chromosomes.append(tempChromosome)
            
            totalMutations -= 1
        
        return chromosomes

    def generate(self,timeTable):
        self.printTimetable(timeTable)
        soft_value,hard_value  = self.calculateFitness(timeTable)
        initialFitnessValue = soft_value + hard_value
        
        currentFitnessValue = initialFitnessValue
        counts = 0
        while(currentFitnessValue > 50 and counts < 200):  
            print("Old Fitness Value: ", currentFitnessValue)
            print("Applying Crossover/Mutation...")
            
            crossoverValues = []
            crossoveredChromosomes = self.crossover(timeTable)
            for chromosome in crossoveredChromosomes:
                soft_value,hard_value = self.calculateFitness(chromosome)
                fitnessValue = soft_value + hard_value
                crossoverValues.append(fitnessValue)
                
            mutationValues = []
            mutatedChromosomes = self.mutation(timeTable)
            for chromosome in mutatedChromosomes:
                soft_value,hard_value = self.calculateFitness(chromosome)
                fitnessValue = soft_value + hard_value
                mutationValues.append(fitnessValue)
        
            minCrossOver = min(crossoverValues)
            minMutation = min(mutationValues)
            
            if(minCrossOver < minMutation):
                if(minCrossOver <= currentFitnessValue):
                    index = crossoverValues.index(minCrossOver)
                    tempTable = crossoveredChromosomes[index]
                    if(currentFitnessValue == minCrossOver):
                        counts += 1
                    currentFitnessValue = minCrossOver

                    timeTable.clear()
                    for day in tempTable:
                        tempDay = []
                        for subject in day:
                            tempDay.append(subject)

                        timeTable.append(tempDay)
                else:
                    counts += 1
                
            else:
                if(minMutation <= currentFitnessValue):
                    index = mutationValues.index(minMutation)
                    tempTable = mutatedChromosomes[index]
                    if(currentFitnessValue == minMutation):
                        counts += 1
                    currentFitnessValue = minMutation

                    timeTable.clear()
                    for day in tempTable:
                        tempDay = []
                        for subject in day:
                            tempDay.append(subject)

                        timeTable.append(tempDay)
                else:
                    counts += 1  
            print("New Fitness Value: ", currentFitnessValue, "\n")
            self.printTimetable(timeTable)

        return timeTable

   