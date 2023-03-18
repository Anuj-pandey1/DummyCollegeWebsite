<?php

    include('../config/db_connect.php');

    $reg_no = $password = '';
    $errors = ['reg_no'=>'', 'password'=>''];

    if(isset($_POST['submit'])){
        
        // check email
        if(empty($_POST['reg_no'])){
            $errors['reg_no'] = 'Registration number is required comment 2020CA290';
        } else{
            $reg_no = $_POST['reg_no'];
            if(!preg_match('/^[0-9]+$/', $reg_no)){
                $errors['reg_no'] = 'Registration number should contain numerical digits only';
            }
        }

        if(empty($_POST['password'])){
            $errors['password'] = 'Password is required ';
        } else{
            $password = $_POST['password'];
        }

        if(array_filter($errors)){     // return true if any non-empty string present inseide $errors
            
        }
        else {
            $reg_no = mysqli_real_escape_string($conn, $_POST['reg_no']);         //prevent from sql injection i.e. pushing malicious code in sql db
            $password = mysqli_real_escape_string($conn, $_POST['password']);

            // if(mysqli_query($conn,$sql)){
                //success
                $url = 'dashboard.php?reg_no='.$reg_no;
                header("Location:  $url" );   //redirecting
            // }
            // else{
            //     echo 'query error: '. mysqli_error($conn);
            // }
        }

} 

?>
<!DOCTYPE html>
<html lang="en">

    <?php include('../templates/header.php') ?>

    <secion class="container grey-text">
            <h4 class="center">Login</h4>
            <form class="white" action="<?php echo $_SERVER['PHP_SELF'] ?>"  method="POST">
                <label>Registration number</label>
                <input type="text" name="reg_no" value=<?php echo htmlspecialchars($reg_no) ?> >
                <div class="red-text">
                    <?php echo htmlspecialchars($errors['reg_no']) ?>
                </div>
                <label>Password</label>
                <input type="text" name="password"  value=<?php echo htmlspecialchars($password) ?>>
                <div class="red-text">
                    <?php echo htmlspecialchars($errors['password']) ?>
                </div>
                <div class="center">
                    <input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
                </div>
            </form>
            <a href="signup.php">Create Account</a>
        </secion>

</body>
</html>