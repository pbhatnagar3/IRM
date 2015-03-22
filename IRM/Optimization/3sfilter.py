#-----------------------------------------------------------------------
# concept_stats.py
#-----------------------------------------------------------------------
# Author: Brian Nemsick (bnensick3)
# Semester: Fall 2014
# Team: Intelligent Review
#-----------------------------------------------------------------------
# Purpose: to calculate static concept statistics in ITS.
# Including:
#	1. Cross Corrolation P(A|B), given tag B is tagged, what is the 
#	probability that A is also tagged.
#	2. Tag use in terms of total questions.
#-----------------------------------------------------------------------

import math
import its_query as itsq

from its_definitions import get_category

class concept_stats(object):
	
	its_query_handle = None
	its_table_handle = None
	
	#-------------------------------------------------------------------
	# Index Defines (Constants)
	#-------------------------------------------------------------------
	NAME = 0;
	TAGS_ID = 1;
	COUNT = 2;
	MAPPED_ID = 3;
	TOTAL_CORR = 4;
	
	#-------------------------------------------------------------------
	# Format of general_stats:
	#-------------------------------------------------------------------
	# 	  Tag ID | Tag Name | Total Questions |
	#-------------------------------------------------------------------
	# Tag0	1    |   Test   |       13        |
	#-------------------------------------------------------------------
	# Tag1 ...                                   
	#-------------------------------------------------------------------
	# Tag2 ...    								   
	#-------------------------------------------------------------------
	general_stats = []
	test = 0
	#Used to map tags_id to indexes for cross correlation
	#cross_key[0] = tag0
	cross_key = []
	
	#total_correlation[0] = total_correlation[cross_key[0]]
	total_correlation = []
	
	#-------------------------------------------------------------------
	# Format of cross_correlation:
	#-------------------------------------------------------------------
	# 	  Tag0 | Tag1 | ... | TagN|
	#-------------------------------------------------------------------
	# Tag0	1  |  ... | ... |
	#-------------------------------------------------------------------
	# Tag1 ...                                   
	#-------------------------------------------------------------------
	# Tag2 ...    								   
	#-------------------------------------------------------------------
	cross_correlation = []
	
	
	def __init__(self):
		
		#Open connection to read from ITS database
		self.its_query_handle = itsq.its_query()
		self.its_query_handle.open_connection()
		
		#Calculate static concept statistics
		self.calc_concept_stats()
		
		#Close connection to read from ITS database
		self.its_query_handle.close_connection()
		
	def calc_concept_stats(self):
		
		#---------------------------------------------------------------
		# Tags Query -> general_stats
		#---------------------------------------------------------------
		# Find tags (actual name), tags_id, total occurrence for ITS
		# assignment 1-7.
		#---------------------------------------------------------------
		questions_id_query =  "SELECT id FROM questions WHERE " + get_category(0)
		tags_stats_query = "SELECT tags.name, questions_tags.tags_id, COUNT(*) FROM questions_tags INNER JOIN tags ON tags.id = questions_tags.tags_id WHERE questions_tags.questions_id IN (" + questions_id_query + ") GROUP BY questions_tags.tags_id HAVING COUNT(*) >= 1" 
		tags_stats = self.its_query_handle.exec_its_query(tags_stats_query)
		
		num_tags = len(tags_stats) #Number of active tags
		
		#Insert into data arrays
		for i in range (0,num_tags):
			self.general_stats.append([str(tags_stats[i][self.NAME]),int(tags_stats[i][self.TAGS_ID]),int(tags_stats[i][self.COUNT]),i,0])
			self.cross_key.append(int(tags_stats[i][self.TAGS_ID]))
		
		
		#---------------------------------------------------------------
		# Cross Corrolation Query -> cross_correlation
		#---------------------------------------------------------------
		# Find cross corrolation of all tags used by ITS
		# Defined as the P(A|B) - Conditional Probability
		#---------------------------------------------------------------
		for B in range (0, num_tags):
			
			#Calculate once to improve computation speed
			current_tags_id = str(self.general_stats[B][self.TAGS_ID])
			current_tags_total_questions = int(self.general_stats[B][self.COUNT])
			
			#Declare an empty 0's array
			current_cross_corr = [0] * num_tags
			cross_corr_concepts_query = "SELECT tags_id,COUNT(*) FROM questions_tags WHERE (questions_id IN (SELECT questions_id FROM questions_tags WHERE tags_id = " + current_tags_id  + " )) AND questions_id IN (SELECT id FROM questions WHERE " + get_category(0) +") GROUP BY tags_id HAVING COUNT(*) >= 1"
			cross_corr_concepts = self.its_query_handle.exec_its_query(cross_corr_concepts_query)
			
			for A in range (0, len(cross_corr_concepts)):
				if (int(cross_corr_concepts[A][0]) != 0):
					ind = self.cross_key.index(int(cross_corr_concepts[A][0]))
					current_cross_corr[ind] = float(float(cross_corr_concepts[A][1])/current_tags_total_questions)
				
			#Add data to class variable
			self.cross_correlation.append(current_cross_corr)
			
			#Total correlation correction for tags with limited questions
			current_total_corr = sum(current_cross_corr)
			
			self.general_stats[B][self.TOTAL_CORR] = current_total_corr 
			self.total_correlation.append(current_total_corr)
			
##################Filter
			#Identify and transform any correlation more than 3 standard deviations outside of the mean
		z = 1 #index to keep track of whether any correlations have been changed
		if z > 0: #loop until there are no correlations > 3sd
			z = 0
			#calculate mean
			m = sum(self.total_correlation)/len(self.total_correlation) 
			#calculate standard deviation
			d = 0 														
			for i in range (0, len(self.total_correlation)):			
				a = self.total_correlation[i] - m						
				d = d + a*a												
			sd = math.sqrt(d/len(self.total_correlation))				
			#calculate upper limit for correlations
			limit = m + 3*sd
			for C in range (0, len(self.general_stats)):
				if (self.general_stats[C][4] > limit) and (2*self.general_stats[C][2] < self.general_stats[C][4]): #check for values outside of 3sd
					n = 0
					mq = 0
					#calculate mean correlation of concepts with the same number of questions
					for i in range (0, len(self.total_correlation)): 
						if (self.general_stats[C][2] == self.general_stats[i][2]):								mq = mq + self.general_stats[i][4]
							n = n+1
					mq = mq/n
					#set correlation to mean correlation of concepts with the same number of questions
					self.general_stats[C][4] = mq
					z = z+1
			self.test = sd
test = concept_stats()


print [test.general_stats]
