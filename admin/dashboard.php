<?php

    include('../config/db_connect.php');

    include('../misc_functions.php');

    function changeSem($conn){
       
            $sql = "UPDATE sem_info SET sem = sem+1, status = 'course entry on'"; 

            $result = mysqli_query($conn,$sql);
            if(!($result))handleError($conn,'  -> 31'); 
    }

    $sql = "SELECT * FROM sem_info";
    $result = mysqli_query($conn,$sql);
    if(!($result))handleError($conn,'  -> 12');

    $sem_info = mysqli_fetch_assoc($result);
    $current_sem = $sem_info['sem'];

    $remaining_proffs =[];
    if(isset($_POST['pending-grading'])){
        
        $sql = "SELECT name FROM proffs p 
                        WHERE p.employee_no IN (SELECT distinct proff_id FROM courses c
                                                WHERE c.id IN 
                                                    (SELECT DISTINCT sub_id  from grades 
                                                        WHERE grade IS NULL AND sem=$current_sem))";

        $result = mysqli_query($conn,$sql);
        if(!($result))handleError($conn,'  -> 31');

        $remaining_proffs = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    if(isset($_POST['show-result'])){
        
        $sql = "SELECT stud_id FROM grades 
        WHERE grade IS NULL"; 

        $result = mysqli_query($conn,$sql);
        if(!($result))handleError($conn,'  -> 31');  

        if(mysqli_num_rows($result)>0){
        echo 'grading is not completed yet.';
        exit;
        }

        $sql = "SELECT c.id,c.name,c.credits,g.grade,sem,stud_id FROM grades g JOIN courses c 
                                                ON c.id=g.sub_id
                    WHERE sem=$current_sem ORDER BY stud_id";    

        $result = mysqli_query($conn, $sql);
        $grades = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $spi_score = $sem_credits = 0;
        $reg_no = '';
        for($i=0; $i<count($grades); $i++){
            $grade = $grades[$i];
            if($grade['stud_id']!=$reg_no){
                if($reg_no==''){
                    $spi_score = $sem_credits = 0;
                    $reg_no = $grade['stud_id'];
                }
                else{
                    $spi = 0;
                    if($sem_credits>0)
                        $spi = $spi_score/$sem_credits;
                    putGrades($sem_credits,$spi,$current_sem,$reg_no,$conn);
                    $spi_score = $sem_credits = 0;
                    $reg_no = $grade['stud_id'];
                }
            }
            $sem_credits += (int)$grade['credits'];
            $spi_score += ((int)$grade['credits']) * ((int) getGradeScore($grade['grade']));
        }
        $spi = 0;
        if($sem_credits>0)
            $spi = $spi_score/$sem_credits;
        if($reg_no!='')
            putGrades($sem_credits,$spi,$current_sem,$reg_no,$conn);
        
        changeSem($conn);


    }

    if(isset($_POST['off'])){
        $sql = "UPDATE sem_info SET status = 'off'"; 

        $result = mysqli_query($conn,$sql);
        if(!($result))handleError($conn,'  -> 31');
    }

    if(isset($_POST['course-entry-on'])){
        $sql = "UPDATE sem_info SET status = 'course entry on'"; 

        $result = mysqli_query($conn,$sql);
        if(!($result))handleError($conn,'  -> 31');
    }

    if(isset($_POST['grade-entry-on'])){
        $sql = "UPDATE sem_info SET status = 'grade entry on'"; 

        $result = mysqli_query($conn,$sql);
        if(!($result))handleError($conn,'  -> 31');
    }

?>

<!DOCTYPE html>
<html lang="en">

    <?php include('../templates/header.php') ?>

    <div style="display: flex;">

        <form class="white" action="<?php echo $_SERVER['PHP_SELF'] ?>"  method="POST" >
            <input type="hidden" name="course-entry-on" value='true'>
            <input type="submit" name="submit" value="Turn ON course entry" class="btn brand z-depth-0">
        </form>

        <form class="white" action="<?php echo $_SERVER['PHP_SELF'] ?>"  method="POST">
            <input type="hidden" name="off" value='true'>
            <input type="submit" name="submit" value="Turn OFF course entry" class="btn brand z-depth-0">
        </form>

    </div>

    <div style="display:flex;">

    <form class="white" action="<?php echo $_SERVER['PHP_SELF'] ?>"  method="POST">
        <input type="hidden" name="grade-entry-on" value='true'>
        <input type="submit" name="submit" value="Turn ON grade entry" class="btn brand z-depth-0">
    </form>

    <form class="white" action="<?php echo $_SERVER['PHP_SELF'] ?>"  method="POST">
        <input type="hidden" name="off" value='true'>
        <input type="submit" name="submit" value="Turn OFF grade entry" class="btn brand z-depth-0">
    </form>

    </div>

    <form class="white" action="<?php echo $_SERVER['PHP_SELF'] ?>"  method="POST">
        <input type="hidden" name="pending-grading" value='true'>
        <input type="submit" name="submit" value="Pending grading" class="btn brand z-depth-0">
        <ul>
            <?php foreach($remaining_proffs as $proff){ ?>
                <li><?php echo $proff['name'] ?></li>
            <?php } ?>
        </ul>
    </form>
    <form class="white" action="<?php echo $_SERVER['PHP_SELF'] ?>"  method="POST">
        <input type="hidden" name="show-result" value='true'>
        <input type="submit" name="submit" value="Show result" class="btn brand z-depth-0">
    </form>


</body>
</html>