<?php

    include('../config/db_connect.php');

    function makeTableHead($head){
        echo '<tr>';
        foreach($head as $key => $body)
            echo '<th> '.strtoupper($key).' </th>';
        echo '</tr>';
    }

    function makeTableBody($info){
        echo '<tr>';
        foreach ($info as $key => $body)
            echo "<td> $body </td>";
        echo '</tr>';
    }

    function makeTable(){
        echo '<div class="col s12 m6">
        <div class="card z-depth-0">
        <div class="">
        <table class="striped">';
    }
    function closeTable(){
        echo '</table style="padding:0px;">
        </div>
        </div>
        </div>';
    }

    function getGradeScore($grade){
        if($grade=='A+') return 10;
        if($grade=='A') return 9;
        if($grade=='B+') return 8;
        if($grade=='B') return 7;
        if($grade=='C') return 6;
        if($grade=='D') return 4;
        return 3;
    }

    function getGrade($marks){
        if($marks>=85)
            return 'A+';
        if($marks>=75)
            return 'A';
        if($marks>=65)
            return 'B+';
        if($marks>=55)
            return 'B';
        if($marks>=45)
            return 'C';
        if($marks>=30)
            return 'D';
        return 'E';
    } 

    function displaySpiCpi($spi, $cpi, $sem,$credits){
        echo '<div class="card deep-orange lighten-4 left-align z-depth-0">SEM '.$sem.' Spi '.$spi.' Cpi '.$cpi.' Credits '.$credits.'</div>';
        // $spi_score = $sem_credits =0;
    }

    function handleError($conn,$line){
        echo 'query error: '. mysqli_error($conn).$line;
        exit(404);
    }



    function putGrades($sem_credits,$sem_score,$sem,$reg_no,$conn){
        
        $sql = "SELECT * FROM cpi_info WHERE stud_id=$reg_no";
        $result = mysqli_query($conn,$sql);
        if(!($result))handleError($conn,'  -> 31');
        $spi_per_sem = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $total_credits = 0;
        $score = 0;
        $cpi=0;
        foreach($spi_per_sem AS $info){
            $total_credits += (int)$info['credits'];
            $score += ((int)$info['credits']) * ((int)$info['spi']);
        }
        
        
        $total_credits += (int)$sem_credits;
        $score += ((int)$sem_credits) * ((int)$sem_score);

        // echo $sem_credits.' ' ;
        // echo $sem_score;
        // echo ' ';
        // echo $total_credits;
        // echo $total_credits;
        
        $cpi = $score/$total_credits;
        $spi = $sem_score;

        $spi = intval($spi * ($p = pow(10, 2))) / $p;
        $cpi = intval($cpi * ($p = pow(10, 2))) / $p;
        
        $sql = "INSERT INTO cpi_info VALUES ('$reg_no','$sem','$sem_credits','$spi','$cpi')";
        $result = mysqli_query($conn,$sql);
        if(!($result))handleError($conn,'  -> 31');
    }

?>