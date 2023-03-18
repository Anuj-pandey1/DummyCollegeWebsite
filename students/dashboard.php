
<?php 

    include('../config/db_connect.php');

    include('../misc_functions.php');

    function semCredits($reg_no,$conn){
        $sql = "SELECT SUM(credits) from grades g LEFT JOIN courses c ON g.sub_id=c.id WHERE g.stud_id=$reg_no GROUP BY (g.sem)";

        $result = mysqli_query($conn,$sql);
        $credits = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // print_r($credits);

        $total = 0;
        // foreach($credits AS $credit){
        //     $total += (int)$credit['credits'];
        // }
        return $total;
    }

    $sql = "SELECT * FROM sem_info";
    $result = mysqli_query($conn,$sql);
    if(!($result))handleError($conn,'  -> 10');

    $sem_info = mysqli_fetch_assoc($result);
    $current_sem = $sem_info['sem'];

    $reg_no = $_GET['reg_no'];

    if(isset($_GET['reg_no'])){



        $sql = "SELECT c.id,c.name,c.credits,g.grade,sem FROM grades g JOIN courses c 
                                                    ON c.id=g.sub_id
                        WHERE stud_id=$reg_no AND sem < $current_sem ORDER BY sem";

        $result = mysqli_query($conn, $sql);

        $grades = mysqli_fetch_all($result, MYSQLI_ASSOC);
         
    }

?>

<!DOCTYPE html>
<html>

    <?php include('../templates/header.php'); ?>

    <div class="container">
        <div class="row">  
    
        <?php
        
        $reg_no = $_GET['reg_no'];

        if(isset($_GET['reg_no'])){

    
            $sql = "SELECT id,name,credits FROM courses 
                        WHERE id IN (SELECT sub_id FROM grades WHERE stud_id='$reg_no' AND sem='$current_sem')";
    
            $result = mysqli_query($conn, $sql);
    
            $courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
            
            echo '<h5>Current courses</h5>';

            makeTable();
            if(count($courses)>0)
            makeTableHead($courses[0]);

            foreach($courses as $course)
                makeTableBody($course);

            closeTable();

        }
        
        ?>

        </div>
    </div>

    <div class="container">
        <div class="row">
            <?php
            $spi_score = $cpi_score = $sem_credits = $total_credits = 0;
            echo '<h4>Result</h4>';

            $sql = "SELECT cpi,spi,sem FROM cpi_info WHERE stud_id=$reg_no ORDER BY sem";
            $result = mysqli_query($conn,$sql);
            if(!($result))handleError($conn,'  ->80');
            $sem_cpi = mysqli_fetch_all($result, MYSQLI_ASSOC);
            
            $passed_till = 0;
            $status = 1;;


            for($i=0; $i<count($grades); $i++){
                
                if($i==0 || $grades[$i]['sem']!=$grades[$i-1]['sem']){
                    if($i!=0){
                        if($status)
                            $passed_till++;
                        $x = $sem_cpi[(int)$grades[$i-1]['sem']-1];
                        displaySpiCpi($x['spi'],$x['cpi'],$x['sem'],semCredits($reg_no,$conn));
                        closeTable();
                    }
                    makeTable();
                    $info = $grades[$i];
                    unset($info['sem']);
                    makeTableHead($info);
                }

                $info = $grades[$i];

                if($info['grade']=='E')
                    $status = 0;

                unset($info['sem']);
                makeTableBody($info);

                $credits = (int)$grades[$i]['credits'];
                $sem_credits += $credits;
                $total_credits += $credits;
                $curr = $credits * getGradeScore($grades[$i]['grade']);
                $spi_score += $curr;
                $cpi_score += $curr;

            }
            if(count($grades)>0){
                if($status)
                    $passed_till++;
                $x = $sem_cpi[$grades[count($grades)-1]['sem']-1];
                displaySpiCpi($x['spi'],$x['cpi'],$x['sem'],semCredits($reg_no,$conn));
            }
            if(count($grades)>0) closeTable();
            
            ?>
        </div>
        <div class="green lighten-4 left-align z-depth-0 margin-30px" style="margin:20px;">PASSES TILL <?php echo $passed_till ?> SEM </div>
    </div>
    


    </body>
</html>