<?php 

    include('../config/db_connect.php');

    include('../misc_functions.php');

    $sql = "SELECT * FROM sem_info";
    $result = mysqli_query($conn,$sql);
    if(!($result))handleError($conn,'  -> 12');

    $sem_info = mysqli_fetch_assoc($result);
    $current_sem = $sem_info['sem'];


    if(isset($_GET['employee_no'])){


        $proff_id = $_GET['employee_no'];
        
        if(isset($_POST['id']) && isset($_POST['name']) && isset($_POST['program']) && isset($_POST['credits'])){
            
            $id = $_POST['id'];
            $name = $_POST['name'];
            $program = $_POST['program'];
            $credits = $_POST['credits'];
            $primary = $_POST['primary'];
            $secondary = $_POST['secondary'];
            $tertiary = $_POST['tertiary'];
            $branch = $_POST['branch'];

            $sql = "SELECT id FROM marks_distribution 
                        WHERE primary_marks='$primary' AND secondary_marks='$secondary' AND tertiary_marks='$tertiary'";

            $result = mysqli_query($conn,$sql);

            if(!($result)) handleError($conn,'31');

            $marks_distribution_id =  mysqli_fetch_assoc($result);

            if(!isset($marks_distribution_id['id'])){
                $sql = "INSERT INTO marks_distribution(primary_marks,secondary_marks,tertiary_marks) VALUE ('$primary','$secondary','$tertiary')";

                $result = mysqli_query($conn,$sql);
                if(!($result)) handleError($conn,'39');

                $marks_distribution_id = mysqli_insert_id($conn);
                if(!($result)) handleError($conn,'42');
            }
            else{
                $marks_distribution_id = $marks_distribution_id['id'];
            }

            $sql = "INSERT INTO courses(id,name,marks_distribution_id,program,branch,proff_id,credits) VALUES ('$id','$name','$marks_distribution_id','$program','$branch','$proff_id','$credits')";
            $result = mysqli_query($conn,$sql);
            if(!($result))handleError($conn,'->53');


           
            $sql = "INSERT INTO grades(stud_id,sub_id,grade,sem)
                    SELECT s.reg_no AS stud_id, '$id' AS sub_id, NULL AS grade , '$current_sem' AS sem
                    FROM students s
                    WHERE s.program='$program' AND s.branch='$branch'";                    //Doubt - why producing all permutation
        
        
            $result = mysqli_query($conn,$sql);
            if(!($result))handleError($conn,'->119');


        }

            
        $sql = "SELECT c.id,name,primary_marks,secondary_marks,tertiary_marks
                    FROM courses c
                    LEFT JOIN marks_distribution md
                    ON c.marks_distribution_id=md.id
                    WHERE c.id IN (SELECT distinct sub_id from grades WHERE sem='$current_sem') AND proff_id='$proff_id'";

        
        $result = mysqli_query($conn,$sql);
        if(!($result)) handleError($conn,' -> 65');

        $courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        if(isset($_POST['fill_value']) && isset($_POST['primary_score']) && isset($_POST['secondary_score'])){

            $reg_no = $_POST['reg_no'];
            $sub_id = $_POST['course_id'];

            $obtained = (int)$_POST['primary_score'];
            $obtained += (int)$_POST['secondary_score'];
            if(isset($_POST['tertiary_score']))
                $obtained += (int)$_POST['tertiary_score'];

            $obtained = getGrade($obtained);

            $sql = "UPDATE grades SET grade = '$obtained'
                        WHERE stud_id='$reg_no' AND sub_id='$sub_id'";
                    
            $result = mysqli_query($conn,$sql);
            if(!($result)) handleError($conn,' -> 65'); 
        }

    }




?>

<!DOCTYPE html>
<html lang="en">

    <?php include('../templates/header.php') ?> 

        <!-- <div  style="display: flex;flex:50%;"> -->
    <div class="row">
        <div style="margin:20px;display: block;">
            
            <?php
            
            if(isset($_GET['employee_no'])){

                $employee_no = $_GET['employee_no']; 


                $sql = "SELECT id,name,credits FROM courses c 
                        WHERE id IN (SELECT distinct sub_id from grades WHERE sem=$current_sem) AND proff_id=$employee_no";
        
                $result = mysqli_query($conn, $sql);
        
                $curr_courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
                // print_r($curr_courses);
                echo '<h5>Current courses</h5>';

                makeTable();
                if(count($curr_courses)>0)
                makeTableHead($curr_courses[0]);

                foreach($curr_courses as $course)
                    makeTableBody($course);

                closeTable();

            }
            
            ?>

        </div>

        <?php if($sem_info['status']=='course entry on'){ ?>

            <form class="white" action="<?php echo $_SERVER['PHP_SELF'].'?employee_no='.$_GET['employee_no'];?>"  method="POST" style="margin:20px;">
            <h5 style="margin:20px;">Add course</h5>
                        <label>Subject code</label>
                        <input type="text" name="id">
                        <label>Course name</label>
                        <input type="text" name="name">
                    
                        <!-- <Select name="course_type" style="display: block;" onchange="<?php echo 'theory';?>">
                            <option value="theory" \>Theory</option>
                            <option value="lab">Lab</option>
                        </Select> -->

                        <div style="display: flex; margin: 10px;">
                            <label>Marks distribution</label>
                            <input type="text" name="primary">
                            <input type="text" name="secondary">
                            <input type="text" name="tertiary">
                        </div>

                        <Select name="program" style="display: block;">
                            <option value="B.Tech">B.Tech</option>
                            <option value="M.Tech">M.Tech</option>
                            <option value="MCA">MCA</option>
                        </Select>

                        <Select name="branch" style="display: block;">
                            <option value="CSE">CSE</option>
                            <option value="IT">IT</option>
                            <option value="ECE">ECE</option>
                            <option value="EE">EE</option>
                            <option value="ME">ME</option>
                            <option value="CHE">CHE</option>
                            <option value="PI">PI</option>
                            <option value="BT">BT</option>
                        </Select>


                        <label>Credits</label>
                        <input type="text" name="credits" >

                        
                        <input type="submit" name="submit" value="submit" class="btn grey lighten-1 z-depth-0">
                
            </form>

        <?php } ?>
    
    </div>


    <?php if($sem_info['status']=='grade entry on'){ ?>

        <form class="white" action="<?php echo $_SERVER['PHP_SELF'].'?employee_no='.$_GET['employee_no'];?>"  method="POST" style="margin:20px;">
                <h5>Add marks</h5>
            <?php foreach($courses as $course){ ?>
                <!-- <div style="display:flex; width=1000px;"> -->
                    <p style="font-size:16px;"><?php echo $course['name']; ?> </p>
                    
                    <!-- <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>"> -->
                    <input type="submit" name="course_id" value="<?php echo $course['id']; ?>">
                <!-- </div> -->
                <?php
                    if(isset($_POST['course_id']) && ($_POST['course_id']==$course['id'])){
                        // echo $_POST['course_id'];
                        $curr_course = $course['id'];
                        $sql = "SELECT reg_no,name
                                    FROM students s
                                    WHERE s.reg_no IN (SELECT stud_id FROM grades 
                                                            WHERE sub_id='$curr_course' AND grade IS NULL)";
            
                        $result = mysqli_query($conn,$sql);
                        if(!($result)) handleError($conn,' -> 65');

                        $student = mysqli_fetch_assoc($result);
                    
                ?>
                
                <?php if($student){ ?>
                
                    <h5><?php echo $student['name']; ?></h5>
                    <h6>Registration number - <?php echo $student['reg_no']; ?></h6>
                    <label>Out of <?php echo $course['primary_marks'];?></label>
                    <input type="number" name="primary_score">
                    <label>Out of <?php echo $course['secondary_marks'];?></label>
                    <input type="number" name="secondary_score">
                    <?php if(isset($course['tertiary_marks']) && $course['tertiary_marks']>0){ ?>
                        <label>Out of <?php echo $course['tertiary_marks'];?></label>
                        <input type="number" name="tertiary_score">
                    <?php } ?>

                    <input type="hidden" name="reg_no" value="<?php echo $student['reg_no']; ?>" >
                    <input type="hidden" name="subject_id" value="<?php echo $course['id']; ?>">
                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">

                    <input type="submit" name="fill_value" value="fill_marks">

                <?php }else{
                    echo 'No student remaining';
                }?>
                



            <?php }} ?>

        </form>

    <?php } ?>

</body>
</html>



