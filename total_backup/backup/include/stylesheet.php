<?php
/*============================================================= /
	LAST_UPDATE: Jun-21-2013
	Author(s): Gregory Krudysz
/*=============================================================*/
?>
        <link rel="stylesheet" href="css/admin.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/login.css" type="text/css" media="screen">

        <link rel="stylesheet" href="css/ITS_concepts.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_BOOK.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_DEBUG.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_logs.css" type="text/css" media="screen">

        <link rel="stylesheet" href="css/ITS_jquery.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_score.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_question.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/ITS_index4.css" type="text/css" media="screen">	
        <link rel="stylesheet" href="css/ITS_tag.css" type="text/css" media="screen"> 
        <link rel="stylesheet" href="css/ITS_navigation.css" type="text/css" media="screen">
        <link rel="stylesheet" href="plugins/tagging/ITS_tagging.css" type="text/css" media="screen">
        <link rel="stylesheet" href="plugins/rating/ITS_rating.css" type="text/css" media="screen">
        <link rel="stylesheet" href="js/jquery-ui-1.8.23.custom/css/ui-lightness/jquery-ui-1.8.23.custom.css" type="text/css">    
        <!-- rating module -->        	
        <script src="js/ITS_admin.js"  type="text/javascript"></script>
        <script src="js/AJAX.js" 	   type="text/javascript"></script>
        <script src="js/ITS_AJAX.js"   type="text/javascript"></script>
        <script src="js/ITS_screen.js" type="text/javascript"></script>
        <script src="js/ITS_QControl.js" type="text/javascript"></script>
        <script src="js/ITS_book.js" type="text/javascript"></script>
        <script src="plugins/tagging/ITS_tagging.js" type="text/javascript"></script>
        <script src="js/ITS_screen.js" type="text/javascript"></script>
		<script src="js/ITS_concepts.js" type="text/javascript"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.23.custom/js/jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.23.custom/js/jquery-ui-1.8.23.custom.min.js"></script>
         
        <link rel="stylesheet" type="text/css" href="plugins/rating/jquery.ui.stars.css?v=3.0.0b38" media="screen">
        <script type="text/javascript" src="plugins/rating/jquery.ui.stars.js?v=3.0.0b38"></script>
        <!--- RATING END -->
        <script type="text/javascript">
            function showFilled(Value) { return (Value > 9) ? "" + Value : "0" + Value; }
            var last, diff;
            $('#contentContainer.innerHTML').change(function() { 
                //alert('a');
                /*
                  var date1 = new Date();
                  var ms1   = date1.getTime();  //alert(ms1);
                  sessionStorage.setItem('TIME0',ms1); 
                 */
            });           
            //---- GOOGLE ANALYTICS ------------------//
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-16889198-1']);
            _gaq.push(['_trackPageview']);
            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();
            //---- GOOGLE ANALYTICS ------------------//        
        </script>
