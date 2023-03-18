
<?php

include('../config/db_connect.php');

$reg_no = $password = $name = $program='';
$errors = ['name'=>'', 'reg_no'=>'', 'password'=>''];

if(isset($_POST['submit'])){
    
    if(empty($_POST['name'])){
        $errors['name'] = 'Name is required comment 2020CA290';
    } else{
        $name = $_POST['name'];
        if(!preg_match('/^[A-Za-z\s]+$/', $name)){
            $errors['name'] = 'Not valid name, should contain only alphabets and space';
        }
    }

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

    if(empty($_POST['program'])){
        $errors['password'] = 'Password is required ';
    }

    if(array_filter($errors)){     // return true if any non-empty string present inseide $errors
        
    }
    else {
        $reg_no = mysqli_real_escape_string($conn, $_POST['reg_no']);         //prevent from sql injection i.e. pushing malicious code in sql db
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $password = mysqli_real_escape_string($conn, $_POST['name']);
        $program = mysqli_real_escape_string($conn, $_POST['program']);
        $branch = mysqli_real_escape_string($conn, $_POST['branch']);

        $sql = "INSERT INTO students(reg_no,name,password,program,branch) VALUES ('$reg_no','$name','$password','$program','$branch')";

        if(mysqli_query($conn,$sql)){
            header('Location:  login.php');   //redirecting
        }
        else{
            echo 'query error: '. mysqli_error($conn);
        }
    }

} 

?>
<!DOCTYPE html>
<html lang="en">

<?php include('../templates/header.php') ?>

<secion class="container grey-text">
        <h4 class="center">Register</h4>
        <form class="white" action="<?php echo $_SERVER['PHP_SELF'] ?>"  method="POST">
            <label>Name</label>
            <input type="text" name="name" value=<?php echo htmlspecialchars($name) ?> >
            <div class="red-text">
                <?php echo htmlspecialchars($errors['name']) ?>
            </div>
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
                <select name="program" style="display: block;">
                        <option value="B.Tech">B.Tech</option>
                        <option value="M.Tech">M.Tech</option>
                        <option value="MCA">MCA</option>
                </select>

                <select name="branch" style="display: block;">
                        <option value="CSE">CSE</option>
                        <option value="IT">IT</option>
                        <option value="ECE">ECE</option>
                        <option value="EE">EE</option>
                        <option value="ME">ME</option>
                        <option value="CHE">CHE</option>
                        <option value="PI">PI</option>
                        <option value="BT">BT</option>
                </select>
            <div class="center">
                <input type="submit" name="submit" value="submit" class="btn brand z-depth-0">
            </div>
        </form>
    </secion>

</body>
</html>