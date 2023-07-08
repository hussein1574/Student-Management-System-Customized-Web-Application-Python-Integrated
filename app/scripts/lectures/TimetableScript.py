import random
import pandas as pd
import xlsxwriter
import os
import copy


class TimetableScript:
    # Constructor that initializes the script directory and reads in data from various Excel files
    def __init__(self, script_dir):
        self.script_dir = script_dir

        # A list of all lectures, labs and sections
        self.allSubjects = []

        # Read in subject details from an Excel file and convert it to a dictionary
        filename = os.path.join(self.script_dir, 'subjects.xlsx')
        self.subjectsDetails = pd.read_excel(filename)
        self.subjects = self.subjectsDetails.set_index(
            'subject').T.to_dict('dict')

        # Read in professor availability data from an Excel file and convert it to a dictionary
        filename = os.path.join(self.script_dir, 'Profs Time.xlsx')
        self.profsData = pd.read_excel(filename)
        self.profs = {}
        for _, row in self.profsData.iterrows():
            prof = row['profs']
            periods = row['periods'].split(',')
            self.profs[prof] = [
                [period.split('-')[0], period.split('-')[1]] for period in periods]
        

        # Read in subject professor data from an Excel file and convert it to a dictionary
        filename = os.path.join(self.script_dir, 'Profs.xlsx')
        self.subjectsProfsData = pd.read_excel(filename)
        self.subjectsProfs = {row['subject']: row['prof'].split(
            ',') for _, row in self.subjectsProfsData.iterrows()}

        # Read in conflict data from an Excel file
        filename = os.path.join(self.script_dir, 'conflict_table.xlsx')
        self.conflictData = pd.read_excel(filename)
        self.conflictData = self.conflictData.set_index(
            self.conflictData.columns[0]).T.to_dict('dict')

        # Read in hall details from an Excel file and convert it to a dictionary
        filename = os.path.join(self.script_dir, 'halls.xlsx')
        self.hallsData = pd.read_excel(filename)
        self.halls = self.hallsData.set_index('hall').T.to_dict('dict')

        # Define the days and periods for the timetable
        filename = os.path.join(self.script_dir, 'days.xlsx')
        self.daysData = pd.read_excel(filename)
        self.days = self.daysData['day'].tolist()

        

        filename = os.path.join(self.script_dir, 'periods.xlsx')
        self.periodsData = pd.read_excel(filename)
        self.periods = self.periodsData['period'].tolist()



    def writeTimeTableToExcelSheet(self, timetable, filename):
        # Create a new Excel workbook and worksheet
        filename = os.path.join(self.script_dir, filename)
        workbook = xlsxwriter.Workbook(filename)
        for worksheet in workbook.worksheets():
            worksheet.clear()
        worksheet = workbook.add_worksheet()

        # Get the days, periods, and halls from the timetable
        days = list(timetable.keys())
        periods = list(timetable[days[0]].keys())
        halls = list(timetable[days[0]][periods[0]].keys())

        # Write the days and periods to the worksheet
        worksheet.write(0, 0, "Period/Day")
        for index, period in enumerate(periods):
            worksheet.write(0, index+1, period)
        for index, day in enumerate(days):
            worksheet.write(index+1, 0, day)

        # Write the subjects for each period and hall to the worksheet
        for day in self.days:
            for period in self.periods:
                periodSubjects = []
                for hall in self.halls:
                    if timetable[day][period][hall]:
                        periodSubjects.append(
                            timetable[day][period][hall] + '-' + hall)
                periodSubjects = "\n".join(periodSubjects)
                worksheet.write(self.days.index(day)+1,
                                self.periods.index(period)+1, periodSubjects)

        # Close the workbook
        workbook.close()

    def isDayBeforeDay(self, day1, day2):
        if (self.days.index(day1) < self.days.index(day2)):
            return True
        else:
            return False

    def isPeriodBeforePeriod(self, period1, period2):
        if (self.periods.index(period1) < self.periods.index(period2)):
            return True
        else:
            return False

    def isTwoPeriodsFollowingEachOther(self, period1, period2):
        if (self.periods.index(period1) + 1 == self.periods.index(period2)
           or self.periods.index(period1) - 1 == self.periods.index(period2)):
            return True
        else:
            return False

    def createTimeTable(self):
        timeTable = {}

        # Get a list of all subjects and their professors
        subjects = list(self.subjects.keys())
        subjectsProfs = copy.deepcopy(self.subjectsProfs)

        # Add additional subjects to the list based on their properties
        for i in range(len(subjects)):
            if self.subjects[subjects[i]]['lab']:
                subjects.append(subjects[i] + "-lab")
            if self.subjects[subjects[i]]['sec']:
                subjects.append(subjects[i] + "-sec")
            if self.subjects[subjects[i]]['lecTime'] > 2:
                subjects.append(subjects[i])
            if self.subjects[subjects[i]]['labTime'] > 2 and self.subjects[subjects[i]]['lab']:
                subjects.append(subjects[i] + "-lab")
            if self.subjects[subjects[i]]['secTime'] > 2 and self.subjects[subjects[i]]['sec']:
                subjects.append(subjects[i] + "-sec")

        # Fill the timetable with subjects
        for day in self.days:
            timeTable[day] = {}
            for period in self.periods:
                timeTable[day][period] = {}
                for hall in self.halls.keys():
                    if subjects:
                        # Choose a random subject from the list
                        subject = random.choice(subjects)
                        if 'sec' not in subject and 'lab' not in subject:
                            # Choose a random professor for the subject
                            if subject in subjectsProfs:
                                prof = random.choice(subjectsProfs[subject])
                                newSubject = subject + '-' + prof

                                # Remove the professor from the list of available professors
                                if len(subjectsProfs[subject]) > 1:
                                    subjectsProfs[subject].remove(prof)

                                # Add the new subject to the timetable and the list of all subjects
                                timeTable[day][period][hall] = newSubject
                                self.allSubjects.append(newSubject)
                            else: 
                                timeTable[day][period][hall] = None
                        else:
                            # Add the subject to the timetable and the list of all subjects
                            timeTable[day][period][hall] = subject
                            self.allSubjects.append(subject)
                        # Remove the subject from the list of available subjects
                        subjects.remove(subject)
                    else:
                        # Add None to the timetable for empty cells
                        timeTable[day][period][hall] = None

        return timeTable

    def checkProfsClash(self, timeTable):
        countClash = 0
        for day in self.days:
            for period in self.periods:
                profsInPeriod = []
                for hall in self.halls:
                    subject = timeTable[day][period][hall]
                    if subject:
                        if 'sec' in subject or 'lab' in subject:
                            continue

                        prof = subject[subject.find('-')+1:]
                        if prof in profsInPeriod:
                            countClash += 120
                        profsInPeriod.append(prof)

        return countClash

    def checkProfsAvailability(self, timeTable):
        CountError = 0
        for day in self.days:
            for period in self.periods:
                for hall in self.halls:
                    subject = timeTable[day][period][hall]
                    if subject:
                        if 'sec' in subject or 'lab' in subject:
                            continue

                        prof = subject[subject.find('-')+1:]
                        profTimes = self.profs[prof]
                        if [day, period] not in profTimes:
                            CountError += 40

        return CountError

    def checkSubjectsClash(self, timeTable):
        countClash = 0
        for day in self.days:
            for period in self.periods:
                subjectsInPeriod = []
                for hall in self.halls:
                    subject = timeTable[day][period][hall]
                    if subject:
                        subject = subject[:subject.find('-')]
                        subjectsInPeriod.append([subject, hall])

                for firstSubject, firstHall in subjectsInPeriod:
                    for secondSubject, secondHall in subjectsInPeriod:

                        if (firstSubject == secondSubject):
                            if (firstHall != secondHall):
                                countClash += 60

                        elif (self.conflictData[firstSubject][secondSubject] != 0):
                            countClash += 60

        return countClash

    def checkDepartment(self, timeTable):
        countError = 0
        error = True
        for day in self.days:
            for period in self.periods:

                for hall in self.halls:
                    subject = timeTable[day][period][hall]

                    if subject:
                        subject = subject[:subject.find('-')]
                        subjectDep = self.subjects[subject]['department'].split(
                            ',')
                            
                        if (self.halls[hall]['department'] in subjectDep):
                            error = False
                        else:
                            error = True

                    
                        if error:
                            countError += 40

        return countError

    def checkSections_LabsFollowingEachOther(self, timeTable):
        countError = 0
        subjects = []
        for day in self.days:
            for period in self.periods:

                for hall in self.halls:
                    subject = timeTable[day][period][hall]

                    if subject and ('sec' in subject or 'lab' in subject):

                        subjects.append([subject, day, period, hall])

        for firstSubject, firstDay, firstPeriod, firstHall in subjects:
            for secondSubject, secondDay, secondPeriod, secondHall in subjects:

                if (firstSubject == secondSubject):
                    if (firstHall == secondHall and firstDay == secondDay and firstPeriod == secondPeriod):
                        continue

                    if (firstDay != secondDay):
                        countError += 40
                        continue
                    elif (not self.isTwoPeriodsFollowingEachOther(firstPeriod, secondPeriod)):
                        countError += 40

        return countError

    def checkLecsFollowingEachOther(self, timeTable):
        countError = 0
        subjects = []
        for day in self.days:
            for period in self.periods:

                for hall in self.halls:
                    subject = timeTable[day][period][hall]

                    if subject and ('prof' in subject):

                        subject = subject[:subject.find('-')]
                        subjects.append([subject, day, period, hall])

        for firstSubject, firstDay, firstPeriod, firstHall in subjects:
            for secondSubject, secondDay, secondPeriod, secondHall in subjects:

                if (firstSubject == secondSubject):

                    if (firstHall == secondHall and firstDay == secondDay and firstPeriod == secondPeriod):
                        continue
                    if (len(self.subjectsProfs[firstSubject]) > 1):
                        continue

                    if (firstDay != secondDay):
                        countError += 10
                    elif (not self.isTwoPeriodsFollowingEachOther(firstPeriod, secondPeriod)):
                        countError += 10

        return countError

    def checkSection_LabBeforeLec(self, timeTable):
        countError = 0
        subjects = []
        for day in self.days:
            for period in self.periods:

                for hall in self.halls:
                    subject = timeTable[day][period][hall]

                    if subject:

                        subjects.append([subject, day, period, hall])

        for firstSubject, firstDay, firstPeriod, firstHall in subjects:
            firstSubjectName = firstSubject[:firstSubject.find('-')]
            for secondSubject, secondDay, secondPeriod, secondHall in subjects:
                secondSubjectName = secondSubject[:secondSubject.find('-')]

                if (firstSubjectName == secondSubjectName):
                    if (firstHall == secondHall and firstDay == secondDay and firstPeriod == secondPeriod):
                        continue

                    firstIsLec = ('prof' in firstSubject)
                    secondIsSec_Lab = (
                        'lab' in secondSubject or 'sec' in secondSubject)

                    if (firstDay != secondDay and firstIsLec and secondIsSec_Lab and self.isDayBeforeDay(secondDay, firstDay)):
                        countError += 20

                    elif (firstIsLec and secondIsSec_Lab and self.isPeriodBeforePeriod(secondPeriod, firstPeriod)):
                        countError += 20

        return countError

    def checkExceedMaxNumberOfStudentsInHall(self, timeTable):
        countExceed = 0
        for day in self.days:
            for period in self.periods:
                for hall in self.halls:
                    subject = timeTable[day][period][hall]

                    if subject:
                        subject = subject[:subject.find('-')]
                        numOfStudents = self.conflictData[subject][subject]
                        hallCapacity = self.halls[hall]['capacity']
                        if (numOfStudents > hallCapacity):
                            countExceed += (numOfStudents - hallCapacity)*0.5

        return countExceed

    def calculateFitness(self, timeTable):

        self.countClashesProfs = self.checkProfsClash(timeTable)
        self.countClashesSubjects = self.checkSubjectsClash(timeTable)
        self.countProfsTimeError = self.checkProfsAvailability(timeTable)
        self.countDepartmentError = self.checkDepartment(timeTable)
        self.countSection_LabError = self.checkSections_LabsFollowingEachOther(
            timeTable)
        self.countLecsError = self.checkLecsFollowingEachOther(timeTable)
        self.countSection_LabBeforeLecError = self.checkSection_LabBeforeLec(
            timeTable)
        self.countExceedMaxNumberOfStudentsInHall = self.checkExceedMaxNumberOfStudentsInHall(
            timeTable)

        soft_value = self.countSection_LabBeforeLecError + \
            self.countExceedMaxNumberOfStudentsInHall + self.countLecsError
        hard_value = self.countClashesSubjects+self.countClashesProfs + \
            self.countProfsTimeError + self.countDepartmentError + self.countSection_LabError

        return soft_value, hard_value

    def crossover(self, timeTable):
        chromosomes = []
        totalCrossovers = 0

        # Count the number of non-empty cells in the timetable
        for day in self.days:
            for period in self.periods:
                for hall in self.halls:
                    if timeTable[day][period][hall]:
                        totalCrossovers += 1
        totalCrossovers -= 3

        # Perform crossover on the timetable
        while totalCrossovers:
            # Create a copy of the timetable
            tempChromosome = {}
            for day in self.days:
                tempChromosome[day] = {}
                for period in self.periods:
                    tempChromosome[day][period] = {}
                    for hall in self.halls:
                        tempChromosome[day][period][hall] = timeTable[day][period][hall]

            # Choose two random locations in the timetable
            randomSubject1 = None
            while not randomSubject1:
                randomDay1 = random.choice(self.days)
                randomPeriod1 = random.choice(self.periods)
                randomHall1 = random.choice(list(self.halls.keys()))
                randomSubject1 = tempChromosome[randomDay1][randomPeriod1][randomHall1]

            randomSubject2 = None
            while not randomSubject2:
                randomDay2 = random.choice(self.days)
                randomPeriod2 = random.choice(self.periods)
                randomHall2 = random.choice(list(self.halls.keys()))
                randomSubject2 = tempChromosome[randomDay2][randomPeriod2][randomHall2]

            # Swap the subjects at the chosen locations
            temp = tempChromosome[randomDay1][randomPeriod1][randomHall1]
            tempChromosome[randomDay1][randomPeriod1][randomHall1] = tempChromosome[randomDay2][randomPeriod2][randomHall2]
            tempChromosome[randomDay2][randomPeriod2][randomHall2] = temp

            # Add the new chromosome to the list of chromosomes
            chromosomes.append(tempChromosome)

            totalCrossovers -= 1

        return chromosomes

    def mutation(self, timeTable):
        chromosomes = []
        totalMutations = 0

        # Count the number of non-empty cells in the timetable
        for day in self.days:
            for period in self.periods:
                for hall in self.halls:
                    if timeTable[day][period][hall]:
                        totalMutations += 1
        totalMutations -= 3

        # Perform mutation on the timetable
        while totalMutations:
            # Create a copy of the timetable
            tempChromosome = {}
            for day in self.days:
                tempChromosome[day] = {}
                for period in self.periods:
                    tempChromosome[day][period] = {}
                    for hall in self.halls:
                        tempChromosome[day][period][hall] = timeTable[day][period][hall]

            # Choose a random location in the timetable
            randomDay1 = random.choice(self.days)
            randomPeriod1 = random.choice(self.periods)
            randomHall1 = random.choice(list(self.halls.keys()))
            randomSubject1 = tempChromosome[randomDay1][randomPeriod1][randomHall1]

            # Change the subject at the chosen location
            if randomSubject1:
                currentSubject = randomSubject1

                # Choose a random subject to replace the current subject
                randomSubject = random.choice(self.allSubjects)
                while randomSubject == currentSubject:
                    randomSubject = random.choice(self.allSubjects)

                # Check if the random subject exists in the timetable and replace it with the current subject
                done = False
                for day in self.days:
                    if done:
                        break
                    for period in self.periods:
                        if done:
                            break
                        for hall in self.halls:
                            if randomSubject == tempChromosome[day][period][hall]:
                                tempChromosome[day][period][hall] = currentSubject
                                done = True
                                break

                # Replace the current subject with the random subject
                tempChromosome[randomDay1][randomPeriod1][randomHall1] = randomSubject

            else:
                # Choose a random subject to add to the timetable
                randomSubject = random.choice(self.allSubjects)

                # Search for the random subject in the timetable and remove it
                done = False
                for day in self.days:
                    if done:
                        break
                    for period in self.periods:
                        if done:
                            break
                        for hall in self.halls:
                            if randomSubject == tempChromosome[day][period][hall]:
                                tempChromosome[day][period][hall] = None
                                done = True
                                break
                # Add the random subject to the chosen location
                tempChromosome[randomDay1][randomPeriod1][randomHall1] = randomSubject

            # Add the new chromosome to the list of chromosomes
            chromosomes.append(tempChromosome)

            totalMutations -= 1

        return chromosomes

    def generate(self, timeTable):
        # Calculate the initial fitness value of the timetable
        soft_value, hard_value = self.calculateFitness(timeTable)
        initialFitnessValue = soft_value + hard_value

        currentFitnessValue = initialFitnessValue
        counts = 0
        while currentFitnessValue > 0 and counts < 1000:
            print("Old Fitness Value: ", currentFitnessValue)
            print("Applying Crossover/Mutation...")

            # Apply crossover to the timetable and calculate the fitness values of the resulting chromosomes
            crossoverValues = []
            crossoveredChromosomes = self.crossover(timeTable)
            for chromosome in crossoveredChromosomes:
                soft_value, hard_value = self.calculateFitness(chromosome)
                fitnessValue = soft_value + hard_value
                crossoverValues.append(fitnessValue)

            # Apply mutation to the timetable and calculate the fitness values of the resulting chromosomes
            mutationValues = []
            mutatedChromosomes = self.mutation(timeTable)
            for chromosome in mutatedChromosomes:
                soft_value, hard_value = self.calculateFitness(chromosome)
                fitnessValue = soft_value + hard_value
                mutationValues.append(fitnessValue)

            # Find the minimum fitness value of the crossover and mutation chromosomes
            minCrossOver = min(crossoverValues)
            minMutation = min(mutationValues)

            # Choose the chromosome with the minimum fitness value and update the timetable if necessary
            if minCrossOver < minMutation:
                if minCrossOver <= currentFitnessValue:
                    index = crossoverValues.index(minCrossOver)
                    tempTable = crossoveredChromosomes[index]
                    if currentFitnessValue == minCrossOver:
                        counts += 1
                    currentFitnessValue = minCrossOver

                    timeTable = {}
                    for day in self.days:
                        timeTable[day] = {}
                        for period in self.periods:
                            timeTable[day][period] = {}
                            for hall in self.halls:
                                timeTable[day][period][hall] = tempTable[day][period][hall]

                else:
                    counts += 1

            else:
                if minMutation <= currentFitnessValue:
                    index = mutationValues.index(minMutation)
                    tempTable = mutatedChromosomes[index]
                    if currentFitnessValue == minMutation:
                        counts += 1
                    currentFitnessValue = minMutation

                    timeTable = {}
                    for day in self.days:
                        timeTable[day] = {}
                        for period in self.periods:
                            timeTable[day][period] = {}
                            for hall in self.halls:
                                timeTable[day][period][hall] = tempTable[day][period][hall]
                else:
                    counts += 1

            print("New Fitness Value: ", currentFitnessValue, "\n")

        return timeTable


