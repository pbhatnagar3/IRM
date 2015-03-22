<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Untitled Document</title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.8.2r1/build/reset/reset-min.css">
    <style media="screen" type="text/css">
    /*by Steve Hatcher 
    http://stever.ca
    http://www.cssstickyfooter.com
    */

    * {
        margin:0;
        padding:0;
    }
    /* must declare 0 margins on everything, also for main layout components use padding, not 
    vertical margins (top and bottom) to add spacing, else those margins get added to total height 
    and your footer gets pushed down a bit more, creating vertical scroll bars in the browser */

    html, body {
        min-height:100%;
        height: 100%;
    }
    #wrap {
        min-height: 100%;
    }
    #main {
        overflow:auto;
        padding-bottom: 50px;
    }  /* must be same height as the footer */
    #footer {
        position: relative;
        margin-top: -50px; /* negative value of footer height */
        height: 50px;
        clear:both;
    }
    /*Opera Fix*/
    body:before {/* thanks to Maleika (Kohoutec)*/
        content:"";
        height:100%;
        float:left;
        width:0;
        margin-top:-32767px;/* thank you Erik J - negate effect of float*/
    }
    /*my styles start from here*/
    #footer {
        text-align:center;
    }
    #wrap {/*background-color:#F60;*/
        margin:0px auto;
        min-height:100%;
        height:100%;
    }
    #header {
        margin:0px auto;
        width:890px;
    }
    #main {
        margin:0px auto;
        width:890px;
    }
    #outerContainer {
        background-color:#999;
        height:100%;
        _min-height:100%;
    }
    #outerShadow {
        width:1000px;
        margin:0 auto;
        background-color:#960;
        height:100%;
        _min-height:100%;
    }
    #outerGap {
        width:970px;
        margin:0px auto;
        background-color:#900;
        _height:100%;
        min-height:100%;
    }
    #outerWrap {
        width:890px;
        margin:0px auto;
        _min-height:100%;
        height:100%;
    }
    #header {
    }
    #topHeader {
        width:890px;
        height:140px;
        background-color:#666
    }
    #BannerHeader {
        width:890px;
        height:320px;
        background-color:#999
    }
    #contents {
        width:830px;
        padding:30px;
        background-color:#CCC;
        min-height:300px;
    }
    #leftSide {
        width:230px;
        height:350px;
        float:left;
        background-color:#09F;
    }
    #rightSide {
        width:570px;
        margin-left:30px;
        float:left;
        background-color:#069;
    }
    .clearboth {
        clear:both;
    }
    </style>

    <!--[if !IE 7]>
        <style type="text/css">
            #wrap {display:table;height:100%}
        </style>
    <![endif]-->

    <script type="text/javascript">

    </script>
    </head>

    <body>
    <div id="outerContainer">
      <div id="outerShadow" class="pngfix">
        <div id="outerGap">
          <div id="outerWrap">
            <div id="wrap">
              <div id="header">
                <div id="topHeader"></div>
                <div id="BannerHeader"></div>
              </div>
              <div id="main">
                <div id="contents"> Integer porta sollicitudin ligula id viverra. In justo diam, tristique in tincidunt nec, cursus eu leo. Nam rutrum viverra dui </div>
              </div>
            </div>
          </div>
          <!--outer wrap --> 

        </div>
        <!--outerGap -->
        <div id="footer"> Sed at elit arcu, vitae malesuada mauris. Nam tincidunt placerat nulla ut lobortis. Nullam id massa id dui venenatis auctor. Nam at mollis tellus. </div>
      </div>
      <!--shadow wrap--> 
    </div>
    <!--outerContainer-->

    </body>
    </html>
