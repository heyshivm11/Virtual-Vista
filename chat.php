




<?php 
  session_start();

  if (isset($_SESSION['username'])) {
    # database connection file
    include 'app/db.conn.php';

    include 'app/helpers/user.php';
    include 'app/helpers/chat.php';
    include 'app/helpers/opened.php';

    include 'app/helpers/timeAgo.php';

    include 'app/helpers/conversations.php';
    include 'app/helpers/last_chat.php';

    if (!isset($_GET['user'])) {
      header("Location: home.php");
      exit;
    }

  # Getting User data data
    $user = getUser($_SESSION['username'], $conn);

    # Getting User conversations
    $conversations = getConversation($user['user_id'], $conn);

    # Getting User data data
    $chatWith = getUser($_GET['user'], $conn);

    if (empty($chatWith)) {
      header("Location: home.php");
      exit;
    }

    $chats = getChats($_SESSION['user_id'], $chatWith['user_id'], $conn);

    opened($chatWith['user_id'], $conn, $chats);

if(isset($_POST['send']))
{


    if (isset($_SESSION['username'])) {

        if (isset($_POST['message']) &&
            isset($_POST['to_id'])) {
        
        # database connection file
        include 'app/db.conn.php';
    
        # get data from XHR request and store them in var
        $message = $_POST['message'];
    
                
        if(!empty(array_filter($_FILES['gallery']['name']))) {
     
            $upload_dir = 'uploads/chatimages/';
            $allowed_types = array('jpg', 'png', 'jpeg', 'gif','jfif', 'pdf');
            $maxsize = 2 * 1024 * 1024;
            // Loop through each file in files[] array
            foreach ($_FILES['gallery']['tmp_name'] as $key => $value) {
                 
                $file_tmpname = $_FILES['gallery']['tmp_name'][$key];
                $file_name = $_FILES['gallery']['name'][$key];
                $file_size = $_FILES['gallery']['size'][$key];
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
     
                // Set upload file path
                $filepath = $upload_dir.$file_name;
     
                // Check file type is allowed or not
                if(in_array(strtolower($file_ext), $allowed_types)) {
     
                    // Verify file size - 2MB max
                    if ($file_size > $maxsize)        
                        echo "Error: File size is larger than the allowed limit.";
     
                    // If file with name already exist then append time in
                    // front of name of the file to avoid overwriting of file
                    if(file_exists($filepath)) {
                        $filepath = $upload_dir.time().$file_name;
                         
                        if( move_uploaded_file($file_tmpname, $filepath)) {
                           // echo "{$file_name} successfully uploaded <br />";
                        }
                        else {                    
                           // echo "Error uploading {$file_name} <br />";
                        }
                    }
                    else {
                     
                        if( move_uploaded_file($file_tmpname, $filepath)) {
                           // echo "{$file_name} successfully uploaded <br />";
                        }
                        else {                    
                            //echo "Error uploading {$file_name} <br />";
                        }
                    }
                }
                else {
                     
                    // If file extension not valid
                    echo "Error uploading {$file_name} ";
                    echo "({$file_ext} file type is not allowed)<br / >";
                }
            }
        }
        else {
             
            // If no files selected
           
        }
    
    
    
        $gallery=$_FILES['gallery']['name'];
        $gallery_img=implode(',',$gallery);
    
        $to_id = $_POST['to_id'];
    
        # get the logged in user's username from the SESSION
        $from_id = $_SESSION['user_id'];
      
        $sql = "INSERT INTO 
               chats (from_id, to_id, message,file) 
               VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $res  = $stmt->execute([$from_id, $to_id, $message, $gallery_img]);
        
        # if the message inserted
        if ($res) {
            /**
           check if this is the first
           conversation between them
           **/
           $sql2 = "SELECT * FROM conversations
                   WHERE (user_1=? AND user_2=?)
                   OR    (user_2=? AND user_1=?)";
           $stmt2 = $conn->prepare($sql2);
           $stmt2->execute([$from_id, $to_id, $from_id, $to_id]);
    
            // setting up the time Zone
            // It Depends on your location or your P.c settings
            // define('TIMEZONE', 'india/kolkata');
            // date_default_timezone_set(TIMEZONE);
    
            $time = date("h:i:s a");
            header("refresh: 0.5");
            
            if ($stmt2->rowCount() == 0 ) {
                # insert them into conversations table 
                $sql3 = "INSERT INTO 
                         conversations(user_1, user_2)
                         VALUES (?,?)";
                $stmt3 = $conn->prepare($sql3); 
                $stmt3->execute([$from_id, $to_id]);
               
            }
            ?>
    

    
        <?php 
         }
      }
    }else {
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat App - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>


  <link rel="stylesheet" 
        href="css/style.css">
  <link rel="icon" href="img/logo.png">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body class="d-flex
             justify-content-center
             align-items-center
             vh-100" id="bg">
<style type="text/css">
body{margin-top:20px;}
/*************** 1.Variables ***************/


/* ------------------ Color Pallet ------------------ */


/*************** 2.Mixins ***************/


/************************************************
    ************************************************
                    Search Box
  ************************************************
************************************************/

.chat-search-box {
    -webkit-border-radius: 3px 0 0 0;
    -moz-border-radius: 3px 0 0 0;
    border-radius: 3px 0 0 0;
    padding: .75rem 1rem;
}

.chat-search-box .input-group .form-control {
    -webkit-border-radius: 2px 0 0 2px;
    -moz-border-radius: 2px 0 0 2px;
    border-radius: 2px 0 0 2px;
    border-right: 0;
}

.chat-search-box .input-group .form-control:focus {
    border-right: 0;
}

.chat-search-box .input-group .input-group-btn .btn {
    -webkit-border-radius: 0 2px 2px 0;
    -moz-border-radius: 0 2px 2px 0;
    border-radius: 0 2px 2px 0;
    margin: 0;
}

.chat-search-box .input-group .input-group-btn .btn i {
    font-size: 1.2rem;
    line-height: 100%;
    vertical-align: middle;
}

@media (max-width: 767px) {
    .chat-search-box {
        display: none;
    }
}


/************************************************
  ************************************************
                  Users Container
  ************************************************
************************************************/

.users-container {
    position: relative;
    padding: 1rem 0;
    border-right: 1px solid #e6ecf3;
    height: 100%;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-direction: column;
    flex-direction: column;
}


/************************************************
  ************************************************
                      Users
  ************************************************
************************************************/

.users {
    padding: 0;
}

.users .person {
    position: relative;
    width: 100%;
    padding: 10px 1rem;
    cursor: pointer;
    border-bottom: 1px solid #f0f4f8;
}

.users .person:hover {
    background-color: #ffffff;
    /* Fallback Color */
    background-image: -webkit-gradient(linear, left top, left bottom, from(#e9eff5), to(#ffffff));
    /* Saf4+, Chrome */
    background-image: -webkit-linear-gradient(right, #e9eff5, #ffffff);
    /* Chrome 10+, Saf5.1+, iOS 5+ */
    background-image: -moz-linear-gradient(right, #e9eff5, #ffffff);
    /* FF3.6 */
    background-image: -ms-linear-gradient(right, #e9eff5, #ffffff);
    /* IE10 */
    background-image: -o-linear-gradient(right, #e9eff5, #ffffff);
    /* Opera 11.10+ */
    background-image: linear-gradient(right, #e9eff5, #ffffff);
}

.users .person.active-user {
    background-color: #ffffff;
    /* Fallback Color */
    background-image: -webkit-gradient(linear, left top, left bottom, from(#f7f9fb), to(#ffffff));
    /* Saf4+, Chrome */
    background-image: -webkit-linear-gradient(right, #f7f9fb, #ffffff);
    /* Chrome 10+, Saf5.1+, iOS 5+ */
    background-image: -moz-linear-gradient(right, #f7f9fb, #ffffff);
    /* FF3.6 */
    background-image: -ms-linear-gradient(right, #f7f9fb, #ffffff);
    /* IE10 */
    background-image: -o-linear-gradient(right, #f7f9fb, #ffffff);
    /* Opera 11.10+ */
    background-image: linear-gradient(right, #f7f9fb, #ffffff);
}

.users .person:last-child {
    border-bottom: 0;
}

.users .person .user {
    display: inline-block;
    position: relative;
    margin-right: 10px;
}

.users .person .user img {
    width: 48px;
    height: 48px;
    -webkit-border-radius: 50px;
    -moz-border-radius: 50px;
    border-radius: 50px;
}

.users .person .user .status {
    width: 10px;
    height: 10px;
    -webkit-border-radius: 100px;
    -moz-border-radius: 100px;
    border-radius: 100px;
    background: #e6ecf3;
    position: absolute;
    top: 0;
    right: 0;
}

.users .person .user .status.online {
    background: #9ec94a;
}

.users .person .user .status.offline {
    background: #c4d2e2;
}

.users .person .user .status.away {
    background: #f9be52;
}

.users .person .user .status.busy {
    background: #fd7274;
}

.users .person p.name-time {
    font-weight: 600;
    font-size: .85rem;
    display: inline-block;
}

.users .person p.name-time .time {
    font-weight: 400;
    font-size: .7rem;
    text-align: right;
    color: #8796af;
}

@media (max-width: 767px) {
    .users .person .user img {
        width: 30px;
        height: 30px;
    }
    .users .person p.name-time {
        display: none;
    }
    .users .person p.name-time .time {
        display: none;
    }
}


/************************************************
  ************************************************
                  Chat right side
  ************************************************
************************************************/

.selected-user {
    width: 100%;
    padding: 0 15px;
    min-height: 64px;
    line-height: 64px;
    border-bottom: 1px solid #e6ecf3;
    -webkit-border-radius: 0 3px 0 0;
    -moz-border-radius: 0 3px 0 0;
    border-radius: 0 3px 0 0;
}

.selected-user span {
    line-height: 100%;
}

.selected-user span.name {
    font-weight: 700;
}

.chat-container {
    position: relative;
    padding: 1rem;
}

.chat-container li.chat-left,
.chat-container li.chat-right {
    display: flex;
    flex: 1;
    flex-direction: row;
    margin-bottom: 40px;
}

.chat-container li img {
    width: 48px;
    height: 48px;
    -webkit-border-radius: 30px;
    -moz-border-radius: 30px;
    border-radius: 30px;
}

.chat-container li .chat-avatar {
    margin-right: 20px;
}

.chat-container li.chat-right {
    justify-content: flex-end;
}

.chat-container li.chat-right > .chat-avatar {
    margin-left: 20px;
    margin-right: 0;
}

.chat-container li .chat-name {
    font-size: .75rem;
    color: #999999;
    text-align: center;
}

.chat-container li .chat-text {
    padding: .4rem 1rem;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    background: #ffffff;
    font-weight: 300;
    line-height: 150%;
    position: relative;
}

.chat-container li .chat-text:before {
    content: '';
    position: absolute;
    width: 0;
    height: 0;
    top: 10px;
    left: -20px;
    border: 10px solid;
    border-color: transparent #ffffff transparent transparent;
}

.chat-container li.chat-right > .chat-text {
    text-align: right;
}

.chat-container li.chat-right > .chat-text:before {
    right: -20px;
    border-color: transparent transparent transparent #ffffff;
    left: inherit;
}

.chat-container li .chat-hour {
    padding: 0;
    margin-bottom: 10px;
    font-size: .75rem;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    margin: 0 0 0 15px;
}

.chat-container li .chat-hour > span {
    font-size: 16px;
    color: #9ec94a;
}

.chat-container li.chat-right > .chat-hour {
    margin: 0 15px 0 0;
}

@media (max-width: 767px) {
    .chat-container li.chat-left,
    .chat-container li.chat-right {
        flex-direction: column;
        margin-bottom: 30px;
    }
    .chat-container li img {
        width: 32px;
        height: 32px;
    }
    .chat-container li.chat-left .chat-avatar {
        margin: 0 0 5px 0;
        display: flex;
        align-items: center;
    }
    .chat-container li.chat-left .chat-hour {
        justify-content: flex-end;
    }
    .chat-container li.chat-left .chat-name {
        margin-left: 5px;
    }
    .chat-container li.chat-right .chat-avatar {
        order: -1;
        margin: 0 0 5px 0;
        align-items: center;
        display: flex;
        justify-content: right;
        flex-direction: row-reverse;
    }
    .chat-container li.chat-right .chat-hour {
        justify-content: flex-start;
        order: 2;
    }
    .chat-container li.chat-right .chat-name {
        margin-right: 5px;
    }
    .chat-container li .chat-text {
        font-size: .8rem;
    }
}

.chat-form {
    padding: 15px;
    width: 100%;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ffffff;
    border-top: 1px solid white;
}

ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}
.card {
    border: 0;
    background: #f4f5fb;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    margin-bottom: 2rem;
    box-shadow: none;
}
</style>
<div class="container">
<nav class="navbar navbar-expand-sm" style="background-color: #0d6efd;margin-top: -100px">

  <div class="container-fluid">
    <!-- Links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="home.php" style="color:white">Profile</a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="logout.php" style="color:white">Logout</a>
      </li>

      <li>
      <input type="submit" class="btn btn-danger" value="Change Theme" onclick="changeStyle()" style="background-color:red;height:50px;width:150px;font-size:15px"></li>
      &nbsp
      <input type="submit" class="btn btn-danger" value="Change Theme" onclick="changeStyle1()" style="background-color:red;height:50px;width:150px;font-size:15px"></li>
      &nbsp
        <input type="submit" class="btn btn-danger" value="Change Theme" onclick="changeStyle2()" style="background-color:red;height:50px;width:150px;font-size:15px"></li>
         <li style="margin-left: 500px;margin-top: 10px;color:white"><b>Welcome  <span><span class="name" style="color:white"><?php echo $_SESSION['name']; ?></b></span></span>
      </li>

      

      
      
    </ul>
  </div>

</nav>
    <!-- Page header start -->
    <div class="page-title">
        <div class="row gutters">
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                <h5 class="title"></h5>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12"> </div>
        </div>
    </div>
    <!-- Page header end -->

    <!-- Content wrapper start -->
    <div class="content-wrapper">
    <form action="#" method="post" enctype="multipart/form-data">
        <!-- Row start -->
        <div class="row gutters">

            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

                <div class="card m-0">

                    <!-- Row start -->
                    <div class="row no-gutters" id="myDiv">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-3 col-3">
                            <div class="users-container">
                                <div class="chat-search-box">
                                    <div class="input-group">
                                        <input class="form-control" placeholder="Search" id="searchText">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-info" id="serachBtn">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <ul class="users" id="chatList">

              <?php if (!empty($conversations)) { ?>
              <?php 

              foreach ($conversations as $conversation){ ?>
                                  <li class="person" data-chat="person1">
                                        <div class="user">
                                            <img src="uploads/<?=$conversation['p_p']?>" alt="Retail Admin">

                                              <?php if (last_seen($conversation['last_seen']) == "Active") { ?>
                                        <div title="online">
                                          <div class="online"></div>
                                        </div>
                                      <?php } ?>


                                            
                                        </div>
                                        <a href="chat.php?user=<?=$conversation['username']?>">
                                        <p class="name-time">
                                            <span class="name" style="color: black"><?=$conversation['name']?></span><br>
                                            <span style="font-weight: normal;"> <?php 
                          echo lastChat($_SESSION['user_id'], $conversation['user_id'], $conn);
                        ?></span>
                                           
                                        </p></a>
                                    </li>
                       <?php }?>   
                                <?php }else{ ?>
            <div class="alert alert-info 
                        text-center">
             <i class="fa fa-comments d-block fs-big"></i>
                       No messages yet, Start the conversation
          </div>
          <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-9 col-9">
                            <div class="selected-user">
                                <span>To: <span class="name"><?=$chatWith['name']?></span></span>
                            </div>


                                 <div class="shadow p-4 rounded
                     d-flex flex-column
                     mt-2 chat-box"
              id="chatBox">
              <?php 
                     if (!empty($chats)) {
                     foreach($chats as $chat){
                      if($chat['from_id'] == $_SESSION['user_id'])
                      { ?>

       
            <p class="rtext align-self-end
                    border rounded p-2 mb-1">
                <?=$chat['message']?> 

                <?php  if ($chat['opened']!='0') { ?> 
                <small class="d-block" style="color:blue">
                  seen
                </small>
                <?php } ?>

                <small class="d-block">
                  <?=$chat['created_at']?>
                </small>        
            </p>
            <style>
.btn {
  background-color: DodgerBlue;
  border: none;
  color: white;
  padding: 12px 30px;
  cursor: pointer;
  font-size: 20px;
}

/* Darker background on mouse-over */
.btn:hover {
  background-color: RoyalBlue;
}
</style>     

            <?php if($chat['file']!=''){?>
            <p class="rtext align-self-end
                    border rounded p-2 mb-1">
                    <button class="btn"><a href="uploads/chatimages/<?=$chat['file']?>" download style="color:white"><i class="fa fa-download" style="color:white"></i> Download File</button></a>

                <small class="d-block">
                  <?=$chat['created_at']?>
                  
                </small>        
            </p>
            <?php } ?>
                    <?php }else{ ?>
          <p class="ltext border 
                   rounded p-2 mb-1">
              <?=$chat['message']?> 
              <small class="d-block">
                <?=$chat['created_at']?>
              </small>        
          </p>

          <?php if($chat['file']!=''){?>
            <p class="ltext border 
                   rounded p-2 mb-1">
                    
                   <button class="btn"><a href="uploads/chatimages/<?=$chat['file']?>" download style="color:white"><i class="fa fa-download" style="color:white"></i> Download File</button></a>

                <small class="d-block">
                  <?=$chat['created_at']?>
                  
                </small>        
            </p>
            <?php } ?>

                    <?php } 
                     }  
              }else{ ?>
               <div class="alert alert-info 
                        text-center">
           <i class="fa fa-comments d-block fs-big"></i>
                 
         </div>
          <?php } ?>
         </div>
<input type="hidden" name="to_id" name="to_id"  value="<?=$chatWith['user_id']?>">
      
           <div class="input-group mb-3">

           <input class="form-control" type="file" id="gallery[]" name="gallery[]" multiple="" style="margin-top:10px" accept="image/*">
           <textarea cols="3"
                       id="message" name="message"
                       class="form-control">
                  </textarea>    
             <!-- <button class="btn btn-primary"
                     id="sendBtn1">
                <i class="fa fa-paper-plane"></i>
             </button> -->
             <input type="submit" class="btn btn-primary" name="send">
         </div>
              </form>
                        </div>
                    </div>
                    <!-- Row end -->
                </div>

            </div>

        </div>
        <!-- Row end -->

    </div>
    <!-- Content wrapper end -->

</div>


 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
  var scrollDown = function(){
        let chatBox = document.getElementById('chatBox');
        chatBox.scrollTop = chatBox.scrollHeight;
  }

  scrollDown();

  $(document).ready(function(){
      
    //   $("#sendBtn").on('click', function(){
    //     message = $("#message").val();
    //     file = $('#image').val();
    //     alert(file);
    //     if (message == "") return;

    //     $.post("app/ajax/insert.php",
    //          {
    //           message: message,
    //           file : file,
    //           to_id: <?=$chatWith['user_id']?>
    //          },
    //          function(data, status){
    //               $("#message").val("");
    //               $("#chatBox").append(data);
    //               scrollDown();
    //          });
    //   });

      /** 
      auto update last seen 
      for logged in user
      **/
      let lastSeenUpdate = function(){
        $.get("app/ajax/update_last_seen.php");
      }
      lastSeenUpdate();
      /** 
      auto update last seen 
      every 10 sec
      **/
      setInterval(lastSeenUpdate, 10000);



      // auto refresh / reload
      let fechData = function(){
        $.post("app/ajax/getMessage.php", 
             {
              id_2: <?=$chatWith['user_id']?>
             },
             function(data, status){
                  $("#chatBox").append(data);
                  if (data != "") scrollDown();
              });
      }

      fechData();
      /** 
      auto update last seen 
      every 0.5 sec
      **/
      setInterval(fechData, 500);
    
    });
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
  $(document).ready(function(){
      
      // Search
       $("#searchText").on("input", function(){
         var searchText = $(this).val();
         if(searchText == "") return;
         $.post('app/ajax/search.php', 
               {
                key: searchText
               },
             function(data, status){
                  $("#chatList").html(data);
             });
       });

       // Search using the button
       $("#serachBtn").on("click", function(){
         var searchText = $("#searchText").val();
         if(searchText == "") return;
         $.post('app/ajax/search.php', 
               {
                key: searchText
               },
             function(data, status){
                  $("#chatList").html(data);
             });
       });


      /** 
      auto update last seen 
      for logged in user
      **/
      let lastSeenUpdate = function(){
        $.get("app/ajax/update_last_seen.php");
      }
      lastSeenUpdate();
      /** 
      auto update last seen 
      every 10 sec
      **/
      setInterval(lastSeenUpdate, 10000);

    });
</script>
</body>
</html>
<?php
  }else{
    header("Location: index.php");
    exit;
  }
 ?>



<script>
    function changeStyle(){
        var element = document.getElementById("myDiv");
        element.style.backgroundColor = "pink";
        document.getElementById("bg").style.backgroundImage = "url(bg1.jpg)";
    }
    </script>


<script>
    function changeStyle1(){
        var element = document.getElementById("myDiv");
        element.style.backgroundColor = "orange";
        document.getElementById("bg").style.backgroundImage = "url(bg2.jpg)";
    }
    </script>



<script>
    function changeStyle2(){
        var element = document.getElementById("myDiv");
        element.style.backgroundColor = "white";
        document.getElementById("bg").style.backgroundImage = "url(bg3.jpg)";
    }
    </script>