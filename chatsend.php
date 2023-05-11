<?php
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
            echo "No files selected.";
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
      
    }
}
?>