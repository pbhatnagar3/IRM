#-----------------------------------------------------------------------
# Function: get_category(self,chapter)
#-----------------------------------------------------------------------
# Purpose: Find the associated assignment-categories of a given 
# assignment.
# Inputs: chapter - chapter #
# Outputs: Associated assignment-categories that are used to 
# construct an ITS assignment
#-----------------------------------------------------------------------
# Note: A Python mirror of the output of /html/classes/its_query.php 
# , getCategory function. A conversion of Greg's code.
# HARDCODED
# chaper 0 corrosponds to assignment 1-7
#-----------------------------------------------------------------------

def get_category(chapter):
	return {
			0: """category REGEXP "(SPEN1$|PreLab01$|Lab1$|Chapter1$|-Mod1$|Complex$|SPEN2$|PreLab02$|Lab2$|Chapter2$|-Mod2$|SPEN3$|PreLab03$|Lab3$|Chapter3$|-Mod3$|SPEN4$|PreLab04$|Lab4$|Chapter4$|-Mod4$|SPEN5$|PreLab05$|Lab5$|Chapter5$|-Mod5$|SPEN6$|PreLab06$|Lab6$|Chapter6$|-Mod6$|SPEN7$|PreLab07$|Lab7$|Chapter7$|-Mod7$)" AND questions.qtype IN ("MC","M","C")""",
			1: """category REGEXP "(SPEN1$|PreLab01$|Lab1$|Chapter1$|-Mod1$|Complex$)" AND questions.qtype IN ("MC","M","C")""",
			2: """category REGEXP "(SPEN2$|PreLab02$|Lab2$|Chapter2$|-Mod2$)" AND questions.qtype IN ("MC","M","C")""",
			3: """category REGEXP "(SPEN3$|PreLab03$|Lab3$|Chapter3$|-Mod3$)" AND questions.qtype IN ("MC","M","C")""",
			4: """category REGEXP "(SPEN4$|PreLab04$|Lab4$|Chapter4$|-Mod4$)" AND questions.qtype IN ("MC","M","C")""",
			5: """category REGEXP "(SPEN5$|PreLab05$|Lab5$|Chapter5$|-Mod5$)" AND questions.qtype IN ("MC","M","C")""",
			6: """category REGEXP "(SPEN6$|PreLab06$|Lab6$|Chapter6$|-Mod6$)" AND questions.qtype IN ("MC","M","C")""",
			7: """category REGEXP "(SPEN7$|PreLab07$|Lab7$|Chapter7$|-Mod7$)" AND questions.qtype IN ("MC","M","C")""",
			8: """category REGEXP "(SPEN1$|PreLab01$|Lab1$|Chapter1$|-Mod1$|Complex$|SPEN2$|PreLab02$|Lab2$|Chapter2$|-Mod2$|SPEN3$|PreLab03$|Lab3$|Chapter3$|-Mod3$|SPEN4$|PreLab04$|Lab4$|Chapter4$|-Mod4$|SPEN5$|PreLab05$|Lab5$|Chapter5$|-Mod5$|SPEN6$|PreLab06$|Lab6$|Chapter6$|-Mod6$)" AND questions.qtype IN ("MC","M","C")""",
	}[chapter]
