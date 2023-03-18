<?php

    include('../config/db_connect.php');

    $admin_id = $password = '';
    $errors = ['admin_id'=>'', 'password'=>''];

    if(isset($_POST['submit'])){
        
        // check email
        if(empty($_POST['admin_id'])){
            $errors['admin_id'] = 'Admin id is required <br />';
        } else{
            $admin_id = $_POST['admin_id'];
            if(!preg_match('/^[0-9]+$/', $admin_id)){
                $errors['admin_id'] = 'Id should contain numerical digits only';
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
            $admin_id = mysqli_real_escape_string($conn, $_POST['admin_id']);         //prevent from sql injection i.e. pushing malicious code in sql db
            $password = mysqli_real_escape_string($conn, $_POST['password']);

            // if(mysqli_query($conn,$sql)){
                //success
                header('Location:  dashboard.php');   //redirecting
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

    <section class="container grey-text">
            <h4 class="center">Login</h4>
            <form class="white" action="<?php echo $_SERVER['PHP_SELF'] ?>"  method="POST">
                <label>Admin number</label>
                <input type="text" name="admin_id" value=<?php echo htmlspecialchars($admin_id) ?> >
                <div class="red-text">
                    <?php echo htmlspecialchars($errors['admin_id']) ?>
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
        </section>

</body>
</html>