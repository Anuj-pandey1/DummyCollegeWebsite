
<?php

include('../config/db_connect.php');

$employee_no = $password = $name = '';
$errors = ['name'=>'', 'employee_no'=>'', 'password'=>''];

if(isset($_POST['submit'])){
    
    if(empty($_POST['name'])){
        $errors['name'] = 'Name is required comment 2020CA290';
    } else{
        $name = $_POST['name'];
        if(!preg_match('/^[A-Za-z\s]+$/', $name)){
            $errors['name'] = 'Not valid name, should contain only alphabets and space';
        }
    }

    if(empty($_POST['employee_no'])){
        $errors['employee_no'] = 'Registration number is required comment 2020CA290';
    } else{
        $employee_no = $_POST['employee_no'];
        if(!preg_match('/^[0-9]+$/', $employee_no)){
            $errors['employee_no'] = 'Registration number should contain numerical digits only';
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
        $employee_no = mysqli_real_escape_string($conn, $_POST['employee_no']);         //prevent from sql injection i.e. pushing malicious code in sql db
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $password = mysqli_real_escape_string($conn, $_POST['name']);
        
        $sql = "INSERT INTO proffs(employee_no,name,password) VALUES ('$employee_no','$name','$password')";

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
        <h4 class="center">Login</h4>
        <form class="white" action="<?php echo $_SERVER['PHP_SELF'] ?>"  method="POST">
            <label>Name</label>
            <input type="text" name="name" value=<?php echo htmlspecialchars($name) ?> >
            <div class="red-text">
                <?php echo htmlspecialchars($errors['name']) ?>
            </div>
            <label>Employee number</label>
            <input type="text" name="employee_no" value=<?php echo htmlspecialchars($employee_no) ?> >
            <div class="red-text">
                <?php echo htmlspecialchars($errors['employee_no']) ?>
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
    </secion>

</body>
</html>