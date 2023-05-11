<?php

require_once 'config2.php';

$permissions = ['email']; //optional

if (isset($accessToken))
{
	if (!isset($_SESSION['facebook_access_token'])) 
	{
		//get short-lived access token
		$_SESSION['facebook_access_token'] = (string) $accessToken;
		
		//OAuth 2.0 client handler
		$oAuth2Client = $fb->getOAuth2Client();
		
		//Exchanges a short-lived access token for a long-lived one
		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
		
		//setting default access token to be used in script
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	} 
	else 
	{
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}
	
	
	//redirect the user to the index page if it has $_GET['code']
	if (isset($_GET['code'])) 
	{
		header('Location: ./');
	}
	
	
	try {
		$fb_response = $fb->get('/me?fields=name,first_name,last_name,email');
		$fb_response_picture = $fb->get('/me/picture?redirect=false&height=200');
		
		$fb_user = $fb_response->getGraphUser();
		$picture = $fb_response_picture->getGraphUser();
		
		$_SESSION['fb_user_id'] = $fb_user->getProperty('id');
		$_SESSION['fb_user_name'] = $fb_user->getProperty('name');
		$_SESSION['fb_user_email'] = $fb_user->getProperty('email');
		$_SESSION['fb_user_pic'] = $picture['url'];

	include 'app/db.conn.php';
			$name =  $_SESSION['fb_user_name'];
	$username =  $_SESSION['fb_user_name'];
	$p_p = $_SESSION['fb_user_pic'];

$content = file_get_contents($p_p);
file_put_contents('uploads/'.$username, $content);

echo $_SESSION['fb_user_name'];
 $sql = "SELECT * 
   	          FROM users
   	          WHERE username=?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$_SESSION['fb_user_name']]);

      if($stmt->rowCount() > 0){
			$user = $stmt->fetch();
      				
            # creating the SESSION
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['p_p'] = $user['p_p'];
            $_SESSION['last_seen'] = $user['last_seen'];

      		header("Location: home.php");
   	    
      }else
      {


 $sql = "INSERT INTO users
                    (name, username, p_p)
                    VALUES (?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $username,$username]);

  $sql  = "SELECT * FROM 
               users WHERE username=?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$username]);

		if($stmt->rowCount() === 1){
		$user = $stmt->fetch();
	   if ($user['username'] === $username) {
           
	   		session_start();	
            # creating the SESSION
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['p_p'] = $user['p_p'];
            $_SESSION['last_seen'] = $user['last_seen'];

            # redirect to 'home.php'
            header("Location: home.php");

  
        }
    }
}
	
		
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		echo 'Facebook API Error: ' . $e->getMessage();
		session_destroy();
		// redirecting user back to app login page
		header("Location: ./");
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK Error: ' . $e->getMessage();
		exit;
	}
} 
else 
{	

	$fb_login_url = $fb_helper->getLoginUrl('http://localhost/chatapp/', $permissions);
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Login with Facebook</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  
  <link href="<?php echo BASE_URL; ?>css/style.css" rel="stylesheet">
  
</head>
<body>




 
	<div class="login-form">
		

			<center><form method="post" 
	 	      action="app/http/auth.php" style="width:400px;text-align: center">
	 		<div class="d-flex
	 		            justify-content-center
	 		            align-items-center
	 		            flex-column">

	 		<img src="logo.png" 
	 		     class="w-251" style="width: 200px">
	 		<h3 class="display-4 fs-1 
	 		           text-center">
	 			       LOGIN</h3>   


	 		</div>
	 		<?php if (isset($_GET['error'])) { ?>
	 		<div class="alert alert-warning" role="alert">
			  <?php echo htmlspecialchars($_GET['error']);?>
			</div>
			<?php } ?>
			
	 		<?php if (isset($_GET['success'])) { ?>
	 		<div class="alert alert-success" role="alert">
			  <?php echo htmlspecialchars($_GET['success']);?>
			</div>
			<?php } ?>
		  <div class="mb-3">
		    <label class="form-label">
		           User name</label>
		    <input type="text" 
		           class="form-control"
		           name="username">
		  </div>

		  <div class="mb-3">
		    <label class="form-label">
		           Password</label>
		    <input type="password" 
		           class="form-control"
		           name="password">
		  </div>
		  
		  <div class="row">
			
			<a href="<?php echo $fb_login_url;?>" class="btn btn-primary btn-block" style="width: 60px;height:40px;margin-left:20px"><i class="fa fa-facebook"></i></a>	
			
			<a href="gmaillogin.php" class="btn btn-danger btn-block" style="width: 60px;margin-left:10px;margin-top:-0.5px;height:40px"><i class="fa fa-google"></i></a>	
		
			<button type="submit" 
		          class="btn btn-primary" style="margin-left: 10px;margin-top: 0px">
		          LOGIN</button>
		
			<a href="signup.php" class="btn btn-success btn-block" style="width: 100px;margin-left: 10px;margin-top:-0px"><b>Sign Up</b></a>
			


		  </div>
		
			
		


		
		</form>
		 
			
		
	
	</div>

<!-- NNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN --> 
      
</body>
</html>

