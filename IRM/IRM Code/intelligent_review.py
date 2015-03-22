#-----------------------------------------------------------------------
# intelligent_review.py
#-----------------------------------------------------------------------
# Author: Brian Nemsick (bnensick3), Pujun, Sen 
# Semester: Spring 2014
# Team: Intelligent Review
#-----------------------------------------------------------------------
# Purpose: Superclass to make review trees.
#-----------------------------------------------------------------------
# Functions:
#	__init__(self)
#   calc_concept_priority_list(self,user_index):
#   fetch_concept(self, current_concept):
#   fetch_questions(self,concept, tier):
#   create_tree(self):
#   calculate_tree(self, tier, debug, base = 10):
#-----------------------------------------------------------------------
# Define:
#
#	#Class handlers retrive data from other scripts 
# 
#	crit_score_handle 
#	its_query_handle 
#	its_table_handle 
#   
#   #Data for buidling the tree
#
#   concept_priority_list 
#	concept_tree 
#	question_tree 
#	question_list = [] #Remaining questions left for user[n]
#
#   #Flags
#	concept_flag = False #Concept list is empty
#
#-----------------------------------------------------------------------

import crit_score
import its_query as itsq
import its_table as itst
from its_definitions import get_category


class intelligent_review(object):
	
	#Define class handles
	crit_score_handle = None
	its_query_handle = None
	its_table_handle = None
	
	#General Data
	semester_tag = None
	T = 0
	total_questions = 0
	total_users = 0
	total_failures = 0
	current_user = 0
	individual_failures = 0
	failure = []
	test_fail = [0,0,0,0,0,0]
	
	#Flags
	concept_flag = False #Concept list is empty

	#Data
	concept_priority_list = []
	question_list = [] #Remaining questions
	
	#Tree Structure
	concept_tree = []
	question_tree = [] 

	def __init__(self,semester_id,T):
		
		#Open class handles
		self.crit_score_handle = crit_score.crit_score(semester_id,T)
		
		#Set data
		self.semester_tag = semester_id
		self.T = T
			
		#Open Database
		self.its_query_handle = itsq.its_query()
		self.its_query_handle.open_connection()	
		self.its_table_handle = itst.its_table()
		self.its_table_handle.open_connection()
		
		#Calculate remaining meta data
		self.create_tree()
			
		#Close database connection
		self.its_query_handle.close_connection()
		self.its_table_handle.close_connection()
		
		print "Done"	
	
	#-------------------------------------------------------------------
	# Function: fetch_concept(self, current_concept):
	#-------------------------------------------------------------------
	# Purpose: Given one concept,find its cloest concept.
	# Inputs: current concept
	# Output: The most relevent concept to the current concept; after fetching, current_concept
	# will be removed removed from the priority list.
	#-------------------------------------------------------------------
	# Note: Called in create_tree
	def fetch_concept(self, current_concept,tier):
		
		#No concepts left logic
		if not(len(self.concept_priority_list)):
			self.concept_flag = True #No concepts left backup check
			return (-1,current_concept)
			
		elif (len(self.concept_priority_list) == 1):
			self.concept_flag = True #No concepts left
			new_concept = self.concept_priority_list[0]
			self.concept_priority_list = []
			return (new_concept,current_concept)
			
		#Find index of current_concept in cross_key
		current_concept_index = self.crit_score_handle.concept_stats_handle.cross_key.index(current_concept)
		
		#Score of all indexs of remaining concepts
		new_concept_score = -1
		new_concept_index = -1
		
		#Power through all remaining concepts
		for ind_concept in range(0,len(self.concept_priority_list)):
			cross_corr_index = self.crit_score_handle.concept_stats_handle.cross_key.index(self.concept_priority_list[ind_concept])
			if (self.crit_score_handle.concept_stats_handle.cross_correlation[current_concept_index][cross_corr_index] > new_concept_score):
				new_concept_score = self.crit_score_handle.concept_stats_handle.cross_correlation[current_concept_index][cross_corr_index]
				new_concept_index = cross_corr_index
				
		new_concept = self.crit_score_handle.concept_stats_handle.cross_key[new_concept_index]
		
		if (new_concept_score == 0):
			self.total_failures = self.total_failures + 1
			self.individual_failures = self.individual_failures + 1
			self.test_fail[tier] = self.test_fail[tier] + 1
			
		#Remove new concept
		self.concept_priority_list.remove(new_concept)
		return (new_concept,current_concept)
				
	#-------------------------------------------------------------------
	# Function: fetch_questions(self,concept, tier)
	#-------------------------------------------------------------------
	# Purpose: Given a tier level, fetch questions for that tier. 
	# Inputs: @param concept, the concept that would be used to fetch corresponding question
	#         @param tier, tier level of the question which will be fetched
	# Output: an question of one particular tier
	#-------------------------------------------------------------------
	# Note: Called in calculate tree
	def fetch_questions(self,concept, tier):

		#If no more concepts left
		if self.concept_flag == 1:
			#If no more questions left
			if len(self.question_list) == 0:
				#Return finished
				return (0,-1)
			#If questions left, return and remove first question
			result = (self.question_list[0], -1)
			if result[0] in self.question_list:
				self.question_list.remove(result[0])
			return result
		
		query_questions_ID_difficulty = "select q_id, difficulty from questions_difficulty where q_id IN (select questions_id from questions_tags where tags_id =" + str(concept) + ")"
		resulting_questions_difficulty = self.its_query_handle.exec_its_query(query_questions_ID_difficulty);
		resulting_questions_difficulty = sorted(resulting_questions_difficulty, key=lambda tup: tup[1], reverse = True)
		average_difficulty = 0
		if len(resulting_questions_difficulty) > 0:
			result = (0, concept)
			for i in resulting_questions_difficulty:
				if i[0] in self.question_list:						
					result = (i[0], concept)
					break
		else:
			result = (0, concept)
		if int(result[0]) in self.question_list:
			self.question_list.remove(int(result[0]))
		return result
	#-------------------------------------------------------------------
	# Function: create_tree(self):
	#-------------------------------------------------------------------
	# Purpose: buidling tree, 1. buidling new tier using data from calculate tree  
	#						  2. after finished one tier, 
	#                            reset data then process to a new tier until complete the tree
	# Inputs: None
	# Output: None
	#-------------------------------------------------------------------
	# Note: Called in contructor
	def create_tree(self):
		
		self.its_table_handle.construct_table("""intelligent_review_"""+self.semester_tag,"""(user INT, question INT, parent INT, concept INT, tier INT, score FLOAT)""")
		
		self.total_users = len(self.crit_score_handle.mapped_user_index)
		
		for user_index in range(0,len(self.crit_score_handle.mapped_user_index)):
		
			#Reset data arrays
			self.concept_priority_list = []
			self.concept_tree = []
			self.question_tree = [] 
			self.question_list = []
			self.concept_flag = False
			self.individual_failures = 0
			
			self.calc_concept_priority_list(self.crit_score_handle.mapped_user_index[user_index],user_index)
			
			tier = 0
		
			while (len(self.question_list) > 0):
				self.calculate_tree(tier,False)
				tier +=1
			
			self.failure.append([self.individual_failures])
			
			for i in range(0,len(self.question_tree)):
				for j in range(0,len(self.question_tree[i])):
					if (self.concept_tree[i][j] == -1):
						break
					if not(i == 0):
						data = "(" + str(self.crit_score_handle.mapped_user_index[user_index]) + "," + str(self.question_tree[i][j]) + "," + str(self.question_tree[i-1][int(j/2)]) + "," + str(self.concept_tree[i][j]) + "," + str(i) + "," + "-1" + ")"
						self.its_table_handle.populate_table("""intelligent_review_"""+self.semester_tag,"""(user,question,parent,concept,tier,score)""", data)
					else:
						data = "(" + str(self.crit_score_handle.mapped_user_index[user_index])  + "," + str(self.question_tree[i][j]) + ",0," + str(self.concept_tree[i][j]) + "," + str(i) + "," + "-1" + ")"
						self.its_table_handle.populate_table("""intelligent_review_"""+self.semester_tag,"""(user,question,parent,concept,tier,score)""", data)
			#print "Step " +  str(user_index+1) + " of " + str(len(self.crit_score_handle.mapped_user_index)) + ": Calculating Tree: for user " + str(self.crit_score_handle.mapped_user_index[user_index]) + " completed"
			
	#-------------------------------------------------------------------
	# Function: calculate_tree(self, tier, debug, base = 10):
	#-------------------------------------------------------------------
	# Purpose: Given a tier level, this funcion simulates the process of buidling each tier of tree
	# Inputs: tier level
	# Output: None 
	#-------------------------------------------------------------------
	# Note: Called in create_tree
	def calculate_tree(self, tier, debug, base = 10):
		
		#Temporary lists
		start_concepts = []
		start_questions = []
		
		#Tier 0 special case
		if (tier == 0):
			for i in range (0,base):
				#Grab concept
				start_concepts.append(self.concept_priority_list[0])
				#Remove from list
				del self.concept_priority_list[0]
		else:
			for i in self.concept_tree[tier-1]:
				#Want 2 questions per concept
				start_concepts.append(i)
				start_concepts.append(i)
		
		#For each starting concept
		for i in range (0,len(start_concepts)):
			#Current concept = position in the for loop
			current_concept = start_concepts[i]
			#Execute until complex condition met
			while (1):
				#Attempt to fetch question
				question, current_concept = self.fetch_questions(current_concept,tier)
				#No question found
				if (question == 0):
					#Transition to fetch concept
					current_concept, old_concept = self.fetch_concept(current_concept,tier)
					#Change current concept
					start_concepts[i] = current_concept
					#If no remaining concepts
					if (current_concept == -1):
						break
				#Found a question
				else:
					break
			start_questions.append(int(question))
		
			
		#Add question and concepts to tree lists
		self.concept_tree.append(start_concepts)
		self.question_tree.append(start_questions)
		
		if (debug):
			print "Tier " + str(tier) + " Concepts:"
			print self.concept_tree[tier]
			print "Tier " + str(tier) + " Questions:"
			print self.question_tree[tier]

	#-------------------------------------------------------------------
	# Function: calc_concept_priority_list(self,mapped_user_index,
	# user_index)
	#-------------------------------------------------------------------
	# Purpose: Turn raw criticalness into a concept priority list.
	# Reset data arrays.
	#-------------------------------------------------------------------
	def calc_concept_priority_list(self,mapped_user_index,user_index):	
		#Grab all questions
		questions = self.its_query_handle.exec_its_query(("SELECT id FROM questions WHERE id IN (SELECT id FROM questions WHERE " + get_category(0) +")"))
		#Sort concepts by criticalness
		criticalness,concepts = zip(*sorted(zip(self.crit_score_handle.cr[user_index], self.crit_score_handle.mapped_concept_index),reverse=True))
		#print concepts
		#Append concepts to concept priority list
		for i in range(0, len(concepts)):
			self.concept_priority_list.append(concepts[i])
		#Append questions to question list
		for i in questions:
			self.question_list.append(i[0])	
		self.total_questions = len(self.question_list)
