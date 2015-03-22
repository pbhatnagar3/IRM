#-----------------------------------------------------------------------
# crit_score.py
#-----------------------------------------------------------------------
# Author: Brian Nemsick (bnensick3)
# Semester: Fall 2014
# Team: Intelligent Review
#-----------------------------------------------------------------------
# Purpose: to calculate criticalness,cr, as defined by the math formula
# Note: see Math for more complete definitions
#-----------------------------------------------------------------------

from scipy import stats
import numpy as np
import concept_stats
import user_stats

class crit_score(object):
	
	concept_stats_handle = None
	user_stats_handle = None
	
	q = [] 
	tcr= []
	tc = [] 
	tcs= [] 
	c = []
	us = []
	uss = []
	cs = []
	css = []
	cr = []
	class_cr = []
	
	mapped_concept_index = []
	mapped_user_index = []
	
	def __init__(self,semester_id,T):
		
		self.concept_stats_handle = concept_stats.concept_stats()
		self.q = self.concept_stats_handle.total_questions[:]
		self.tc = self.concept_stats_handle.total_correlation[:]	
		self.tcr = self.concept_stats_handle.total_correlation[:]
		self.mapped_concept_index = self.concept_stats_handle.cross_key[:]
		self.filter_total_corr()
		self.user_stats_handle = user_stats.user_stats(semester_id)
		self.c = self.user_stats_handle.individual_level_its_completion_rate[:].tolist()
		self.us = self.user_stats_handle.individual_level_concept_proficiency[:].tolist()
		self.uss = self.user_stats_handle.individual_level_concept_proficiency[:].tolist()
		self.cs = self.user_stats_handle.class_level_concept_proficiency[:]
		self.filter_class_stat()
		self.mapped_user_index = self.user_stats_handle.users
		self.dist_score()
		self.calc_crit_score(T)
		
	def filter_total_corr(self):
		
		for i in range (0,len(self.tc)):
			if (self.tc[i] >= np.percentile(self.tc,90) and self.q[i] <= np.percentile(self.q,25)):
				self.tc[i] = self.tc[i] * self.q[i]/(self.q[i] + 1)
	
	def filter_class_stat(self):
		for i in range (0,len(self.cs)):
			if (self.cs[i] <= np.percentile(self.cs,12.5)):
				self.cs[i] = self.cs[i] + (max(self.cs) - min(self.cs))/4
			
	
	def dist_score(self):
		self.tcs = stats.norm.cdf(np.multiply(stats.zscore(self.tc),-1))
		self.css = stats.norm.cdf(np.multiply(stats.zscore(self.cs),-1))
		
		for i in range(0,len(self.us)):
			if (sum(self.us[i]) != 0):
				self.uss[i] = stats.norm.cdf(np.multiply(stats.zscore(self.us[i]),-1))

	def calc_crit_score(self,T):
		current_cr = [0] * len(self.mapped_concept_index)
		for row in range(0,len(self.mapped_user_index)):
			for col in range(0,len(self.mapped_concept_index)):
				current_cr[col] = T * self.tcs[col] + (1-T) * (self.c[row][col]*self.uss[row][col] + (1-self.c[row][col])*self.css[col])
			self.cr.append(current_cr)
			current_cr = [0] * len(self.mapped_concept_index)
		for col in range(0,len(self.mapped_concept_index)):
			current_cr[col] = T * self.tcs[col] + (1-T) * self.css[col]
		self.class_cr = current_cr
