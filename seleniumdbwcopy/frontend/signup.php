<?php
$insert=false;
if(isset($_POST['name'])){
    $server="localhost";
    $username="root";
    $password="";

    $con=mysqli_connect($server,$username,$password);

    if(!$con){
        die("connection to this database failed due to".mysqli_connect_error());
    }
    // echo "Success Connecting to database";

    $name=$_POST['name'];
    $gender=$_POST['gender'];
    $age=$_POST['age'];
    $email=$_POST['email'];
    $phone=$_POST['phone'];

    $sql="INSERT INTO `survey`.`ai` (`Name`, `Age`, `Gender`, `Email`, `Phone`, `dt`) VALUES ('$name','$age', '$gender', '$email', '$phone', current_timestamp());";
    // echo $sql;

    if($con->query($sql) == true){
        // echo "Successfully inserted";
        $insert=true;
    }else{
        echo "ERROR: $sql  <br> $conn->error";
    }

    $con->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PriceBite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans&family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="signup.css">
    <link rel="stylesheet" href="style.css">
    
</head>
<body> 
    
    <nav class="navbar">
            <div class="navbar-left">
                <a href="index.html" class="navbar-logo h-font">PriceBite</a>
            </div>

            <div class="navbar-right">
                <a href="./signin.php" class="navbar-button">Login</a>
            </div>
        </nav>

    <div class="container">
        <div class="header">
        <h3>Sign Up</h3>
        <div class="underline-one"></div>
        
    
     </div>
        <div class="form-container" style="margin-top:1rem">
        <br/>
        
        <form action="index.php" method="POST">
            <input type="text" name="name" id="name" placeholder="Enter your name:">
            <input type="text" name="age" id="age" placeholder="Enter your Age:">
            <input type="email" name="email" id="email" placeholder="Enter your Email:">
            <input type="password" name= "password" placeholder="Enter Password:">
            <input type="phone" name="phone" id="phone" placeholder="Enter your Phone No:">
            <button class="btn">Submit</button>
            
        </form>
        <?php
        if($insert==true){
            echo '<p class="submitMsg">Sign Up successful</p>';
        }
            
        ?>
    </div>
    </div>
    

    
</body>
</html>
