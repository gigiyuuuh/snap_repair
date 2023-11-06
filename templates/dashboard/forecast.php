<?php
    // include("../../server/connection/db_connection.php");
    $query = mysqli_query($conn, "SELECT * FROM tbl_damagereports ORDER BY DAMAGE_DATE ASC");
    $minDate = mysqli_fetch_assoc($query);
    $query = mysqli_query($conn, "SELECT * FROM tbl_damagereports ORDER BY DAMAGE_DATE DESC");
    $maxDate = mysqli_fetch_assoc($query);

    $minDate = date_format(date_create($minDate["DAMAGE_DATE"]), "Y");
    $maxDate = date_format(date_create($maxDate["DAMAGE_DATE"]), "Y");
    $dateValues = array();
    
    $currentDate = $minDate;
    while($currentDate <= $maxDate){
        $query = mysqli_query($conn, "SELECT COUNT(*) AS COUNT FROM tbl_damagereports WHERE DAMAGE_DATE LIKE '%$currentDate%'");
        $result = mysqli_fetch_assoc($query);
        array_push($dateValues, $result["COUNT"]);
        $currentDate++;
    }

    $factorial = 0;

    for($i = 1; $i <= count($dateValues); $i++){
        $factorial += $i;
    }
    
    $d_weightedMovingAve = 0;

    for($i = 0; $i < count($dateValues); $i++){
        $weight = ($i + 1)/$factorial;
        $weightedAve = $weight * $dateValues[$i];
        $d_weightedMovingAve += $weightedAve;
    }
?>

<?php
    // include("../../server/connection/db_connection.php");
    $query = mysqli_query($conn, "SELECT * FROM tbl_damagereports ORDER BY DAMAGE_DATE ASC");
    $minDate = mysqli_fetch_assoc($query);
    $query = mysqli_query($conn, "SELECT * FROM tbl_damagereports ORDER BY DAMAGE_DATE DESC");
    $maxDate = mysqli_fetch_assoc($query);

    $minDate = date_format(date_create($minDate["DAMAGE_DATE"]), "Y");
    $maxDate = date_format(date_create($maxDate["DAMAGE_DATE"]), "Y");
    $budget = array();
    
    $currentDate = $minDate;
    while($currentDate <= $maxDate){
        $query = mysqli_query($conn, "SELECT SUM(REPAIR_COST) AS COUNT FROM tbl_damagereports WHERE DAMAGE_DATE LIKE '%$currentDate%'");
        $result = mysqli_fetch_assoc($query);
        array_push($budget, $result["COUNT"]);
        $currentDate++;
    }

    $factorial = 0;

    for($i = 1; $i <= count($dateValues); $i++){
        $factorial += $i;
    }
    
    $b_weightedMovingAve = 0;

    for($i = 0; $i < count($budget); $i++){
        $weight = ($i + 1)/$factorial;
        $weightedAve = $weight * $budget[$i];
        $b_weightedMovingAve += $weightedAve;
    }
?>