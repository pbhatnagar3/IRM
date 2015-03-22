########################################################################
# The following class is a python implementation of the existing user_stat.php
#file and is used to calculate the user statistics for all the students taking
#the class.
#Authors: Brian Nemsick and Pujun Bhatnagar and Sen Lin
#Date: Sept 09 2014
#
#
########################################################################
#importing its_query for communicating with the database
import its_query as itsq
from its_definitions import get_category
import numpy

#class implementation of user_stats (similar to user_stats.php)
class user_stats(object):
#making a debug handle
	debug = 0;
#delaring variables
	its_query_handle = None
#last assignment number
	last_assignment = 8
#the list that contains all the user sorted numerically. Key to how things are arranged in the follow matrices
	users = []
#the list that contains all the concept sorted numerically. Key to how things are arranged in the follow matrices
	concepts = []

#number of time concept "N" appear across all questions	
#Store as array but sorted by its concept's numeric order, value = frequnency
	concept_frequency = []
	
#the class level proficiency for concept "N"
#Store as array but sorted by its concept's numeric order, value = proficiency
	class_level_concept_proficiency = []
	
#the individual level proficiency for concept "N"
#Store as 2D array; rows = user, columns = concepts, X(user,concept) = proficiency;
#Note particular proficiency is mapped to this matric by the numeric orders of its userID and ConceptID
	individual_level_concept_proficiency = []
	
#the individual level ITS completion rate for concept "N"
#Store as 2D array; rows = user, columns = concepts, X(user,concept) = completion rate;
#Note particular completion rate is mapped to this matric by the numeric orders of its userID and ConceptID
	individual_level_its_completion_rate = []
	
# constructor of the class
	def __init__(self,semester_id):
			#Open Database
			user_stats.its_query_handle = itsq.its_query()
			user_stats.its_query_handle.open_connection()
			
			self.getUsers(semester_id)
			self.getConcepts()
			self.calculate_individual_and_class_level_concept_proficiency()
			self.calculate_concept_completion_rate_for_all_users()
	
			#Close Database
			user_stats.its_query_handle.close_connection()
	#-------------------------------------------------------------------
	# Function: getUsers(self, semester)
	#-------------------------------------------------------------------
	# Purpose: this method gets all the users for a given semester and saves it into the users list
	# Inputs: semesterID
	# Output: None
	#-------------------------------------------------------------------
	# Note: Called in the constructor
	#-------------------------------------------------------------------
	def getUsers(self, semester):
		#figure out all the users
		query = "SELECT id from users where status = \"" + str(semester) +"\" ;"
		#one can uncomment it for debugging purposes
		result = user_stats.its_query_handle.exec_its_query(query)
		for i in result:
			self.users.append(int(i[0]))
		self.users.sort()
		#self.users = []
		#self.users.append(1758)
	#-------------------------------------------------------------------
	# Function: getConcepts(self,s_id = 1341, filter_coeff = 3):
	#-------------------------------------------------------------------
	# Purpose: #this function gets all the important concepts for each assignment
	# Inputs: s_id = 1341, filter_coeff = 3
	# Output: None
	#-------------------------------------------------------------------
	# Note: Called in the constructor
	#-------------------------------------------------------------------
	def getConcepts(self,s_id = 1341, filter_coeff = 1):
		#Find concepts, number of questions	
		query = 'SELECT tags_id,COUNT(*) ' \
				'FROM questions_tags ' \
				'INNER JOIN questions ON questions_tags.questions_id = questions.id ' \
				'WHERE ' + get_category(0) + ' ' \
				'GROUP BY tags_id HAVING COUNT(*) >= ' + str(filter_coeff)+ ' ' \
				'ORDER BY tags_id'
		query_result = user_stats.its_query_handle.exec_its_query(query)
		for j in range(1, len(query_result)):
				self.concepts.append(query_result[j][0])
				self.concept_frequency.append(query_result[j][1])
	#-------------------------------------------------------------------
	# Function: calculate_individual_and_class_level_concept_proficiency(self):
	#-------------------------------------------------------------------
	# Purpose: #this function gets all the inidivual level concept proficiency
	# Inputs: None
	# Output: None
	#-------------------------------------------------------------------
	# Note: Called in the constructor
	#-------------------------------------------------------------------
	def calculate_individual_and_class_level_concept_proficiency(self):
		#Something interesting from numpy, initilize to zeros
		self.class_level_concept_proficiency = numpy.zeros(len(self.concepts))
		self.individual_level_concept_proficiency = numpy.zeros((len(self.users),len(self.concepts)))
		factorList = numpy.zeros(len(self.concepts))
		#Find user proficiencyf
		for userIndex in range(0, len(self.users)):
			user = self.users[userIndex]
			for conceptIndex in range(0, len(self.concepts)):
				concept = self.concepts[conceptIndex]
				query =  'SELECT avg(score) ' \
						 'FROM stats_' + str(user) + ' AS user ' \
						 'INNER JOIN questions_tags AS qt ON user.question_id = qt.questions_id '\
						 'WHERE tags_id = ' + str(concept)
				results = user_stats.its_query_handle.exec_its_query(query)
				if (str(results[0]) == '(None,)'):
					factorList[conceptIndex] += 1
					proficiency = 0
				else:
					proficiency = float(results[0][0])
				#this is to accumulate concept score for the class level,
				#then at the end divide total score of each concept by the class size
				self.class_level_concept_proficiency[conceptIndex] += proficiency
				self.individual_level_concept_proficiency[userIndex][conceptIndex] = proficiency
		#Average the score
		for index in range(0, len(self.class_level_concept_proficiency)):
			length = len(self.users) - factorList[index]
			if (length > 0):
				self.class_level_concept_proficiency[index] = self.class_level_concept_proficiency[index]/length
			else:
				self.class_level_concept_proficiency[index] = 0
	#-------------------------------------------------------------------
	# Function: calculate_concept_completion_rate_for_all_users(self):
	#-------------------------------------------------------------------
	# Purpose: #this function calculates concept completion rates for all users
	# Inputs: None
	# Output: None
	#-------------------------------------------------------------------
	# Note: Called in the constructor
	#-------------------------------------------------------------------
	def calculate_concept_completion_rate_for_all_users(self):
		self.individual_level_its_completion_rate = numpy.zeros((len(self.users),len(self.concepts)))
		for userIndex in range(0, len(self.users)):
			user = self.users[userIndex]
			for conceptIndex in range(0, len(self.concepts)):
				concept = self.concepts[conceptIndex]
				query = 'SELECT count(score)/count(distinct question_id) ' \
						 'FROM stats_' + str(user) + ' AS user ' \
						 'INNER JOIN questions_tags AS qt ON user.question_id = qt.questions_id '\
						 'WHERE tags_id = ' + str(concept)
				results = user_stats.its_query_handle.exec_its_query(query)
				if (str(results[0]) == '(None,)'):
					self.individual_level_its_completion_rate[userIndex][conceptIndex] = 0 
				#Student answeres quesitons that they have already answered before
				#Verified
				elif (float(results[0][0]) > 1):
					self.individual_level_its_completion_rate[userIndex][conceptIndex] = 1
				else:
					self.individual_level_its_completion_rate[userIndex][conceptIndex] = float(results[0][0])
