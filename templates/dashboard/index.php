<?php
if(isset($_POST["page"]) && $_POST["page"] == "dashboard"){
    include("../../server/connection/db_connection.php");
    include("forecast.php");
}
else{
    include("templates/dashboard/forecast.php");
}
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script src="templates/dashboard/script.js"></script>

<h4><b>Dashboard</b></h4>

<div class="row">
    <div class="col">
        <div class="container card-container">
            <i class='bx bx-dollar-circle nav_icon' style="font-size: 30px"></i> <br>
            <small class="text-light">Total No. of Assets</small><br>
            <?php
            $query = mysqli_query($conn, "SELECT * FROM tbl_inventory");
            ?>
            <h4><b><?php echo mysqli_num_rows($query); ?></b></h4>
        </div>
    </div>
    <div class="col">
        <div class="container card-container">
            <i class='bx bx-money nav_icon' style="font-size: 30px"></i> <br>
            <small class="text-light">Total Assets Value</small><br>
            <?php
            $query = mysqli_query($conn, "SELECT SUM(PURCHASE_COST) AS TOTAL_ASSET_VALUE FROM tbl_inventory");
            $result = mysqli_fetch_assoc($query);
            ?>
            <h4><b>₱ <?php echo number_format($result["TOTAL_ASSET_VALUE"]); ?></b></h4>
        </div>
    </div>
    <div class="col">
        <div class="container card-container">
            <i class='bx bx-credit-card-alt nav_icon' style="font-size: 30px"></i> <br>
            <small class="text-light">Total Spent in Asset Repairs</small><br>
            <?php
            $query = mysqli_query($conn, "SELECT SUM(REPAIR_COST) AS TOTAL_REPAIR_COST FROM tbl_damagereports");
            $result = mysqli_fetch_assoc($query);
            ?>
            <h4><b>₱ <?php echo number_format($result["TOTAL_REPAIR_COST"]); ?></b></h4>
        </div>
    </div>
    <div class="col">
        <div class="container card-container">
            <i class='bx bxs-report nav_icon' style="font-size: 30px"></i> <br>
            <small class="text-light">Total No. of Reported Damages</small><br>
            <?php
            $query = mysqli_query($conn, "SELECT * FROM tbl_damagereports");
            ?>
            <h4><b><?php echo mysqli_num_rows($query); ?></b></h4>
        </div>
    </div>
</div>

<br>

<div class="row">
    <div class="col-3">
        <div class="container card-container" style="padding-left: 0; padding-right: 0">
            <center>
                <b>Asset Status</b>
                <div id="assetStatusChart">
                </div>
            </center>
        </div>
    </div>
    <div class="col">
        <div class="container card-container" style="padding-left: 0; padding-right: 0">
            <center>
                <b>Forecasted Asset Damages</b>
                <div id="damageForecastChart"></div>
            </center>
        </div>
    </div>
    <div class="col">
        <div class="container card-container"  style="padding-left: 0; padding-right: 0">
            <center>
                <b>Prescribed Budget For Next Year</b>
                <div id="budgetForecastChart" style="width: 98%"></div>
            </center>
        </div>
    </div>
</div>

<br>

<div class="row">
    <div class="col">
        <div class="container card-container" style="padding-left: 0; padding-right: 0">
            <center>
                <b>Top 10 Reported Damages Based on Asset types</b>
                <div id="damagesOnAssetTypes" style="width: 98%"></div>
            </center>
        </div>
    </div>
    <div class="col">
        <div class="container card-container"  style="padding-left: 0; padding-right: 0">
            <center>
                <b>Most Frequent Damaged Asset Component</b>
                <div id="frequentDamagedComponent" style="width: 98%"></div>
            </center>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col">
        <div class="container card-container" style="padding-left: 0; padding-right: 0">
            <center>
                <b> Top 5 Department with Most Damages </b>
                <div id="damagesPerDepartment" style="width: 98%"></div>
            </center>
        </div>
    </div>
<br>
<style>
    .card-container{
        background: #313131;
        padding: 15px 10px;
        border-radius: 10px;
    }
    #main-content{
        height: 100vh;
    }
</style>
<br>


<!-- Load Asset Status Chart -->
<script type="text/javascript">
    // Load google charts
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    // Draw the chart and set the chart values
    function drawChart() {
        <?php
            $qfunctional = mysqli_query($conn, "SELECT COUNT(*) as COUNT FROM tbl_inventory WHERE STATUS = 'functional'");
            $functional = mysqli_fetch_assoc($qfunctional);
            $qnonfunctional = mysqli_query($conn, "SELECT COUNT(*) as COUNT FROM tbl_inventory WHERE STATUS = 'non functional'");
            $nonfunctional = mysqli_fetch_assoc($qnonfunctional);
        ?>
        var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            ['Working', <?php echo $functional["COUNT"]; ?>],
            ['Damaged', <?php echo $nonfunctional["COUNT"]; ?>],
        ]);

        // Optional; add a title and set the width and height of the chart
        var options = {
            pieHole: 0.4,
            titleTextStyle: {
                color: 'white'
            },
            backgroundColor: '#313131',
            legend: {
                position: 'bottom', 
                textStyle: {
                    color: 'white', 
                }
            }
        };

        // Display the chart inside the <div> element with id="piechart"
        var chart = new google.visualization.PieChart(document.getElementById('assetStatusChart'));
        chart.draw(data, options);
    }
</script>

<!-- Load Forecasted Damage Asset Chart -->
<script>
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Year', 'Reported Damages', 'Forecast'],
            <?php
                $maxDate = intval(date_format(date_create(), "Y"));
                $minDate = $maxDate - 5;

                while($minDate <= $maxDate){
                    $query = mysqli_query($conn, "SELECT COUNT(*) AS COUNT FROM tbl_damagereports WHERE DAMAGE_DATE LIKE '%$minDate%'");
                    $count = mysqli_fetch_assoc($query);
                    
                    echo "['" . $minDate . "', " . number_format($count["COUNT"], 0, '.', '') . ", " . number_format($count["COUNT"], 0) ."],";
                    $minDate++;
                }
                echo "['" . $minDate . "', " . number_format($d_weightedMovingAve, 0, '.', '') . ", " . number_format($d_weightedMovingAve, 0) ."],";
            ?>
        ]);

        var options = {
            chartArea: {width: '70%'},
            hAxis: {
                title: 'Year',  
                titleTextStyle: {
                    color: '#fff'
                },
                textStyle: {
                    color: 'white'
                },
            },
            vAxis: {
                title: 'Reported Damaged Assets',
                minValue: 0,
                titleTextStyle: {
                    color: '#fff'
                },
                textStyle: {
                    color: 'white'
                },
            },
            backgroundColor: '#313131',
            legend: {
                position: 'none', 
            }
        };

        var chart = new google.visualization.AreaChart(document.getElementById('damageForecastChart'));
        chart.draw(data, options);
    }
</script>

<!-- Forecasted Budget Chart -->
<script>
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Year', 'Budget'],
            <?php
                $maxDate = intval(date_format(date_create(), "Y"));
                $minDate = $maxDate - 5;

                while($minDate <= $maxDate){
                    $query = mysqli_query($conn, "SELECT SUM(REPAIR_COST) AS COUNT FROM tbl_damagereports WHERE DAMAGE_DATE LIKE '%$minDate%'");
                    $count = mysqli_fetch_assoc($query);
                    
                    echo "['" . $minDate . "', " . number_format($count["COUNT"], 0, '.', '') . "],";
                    $minDate++;
                }
                echo "['" . $minDate . "', " . number_format($b_weightedMovingAve, 0, '.', '') . "],";
            ?>
        ]);

        var options = {
            chartArea: {width: '70%'},
            hAxis: {
                title: 'Year',  
                titleTextStyle: {
                    color: '#fff'
                },
                textStyle: {
                    color: 'white'
                },
            },
            vAxis: {
                title: 'Budget',
                minValue: 0,
                titleTextStyle: {
                    color: '#fff'
                },
                textStyle: {
                    color: 'white'
                },
            },
            backgroundColor: '#313131',
            legend: {
                position: 'none', 
            }
        };

        var chart = new google.visualization.AreaChart(document.getElementById('budgetForecastChart'));
        chart.draw(data, options);
    }
</script>

<!-- Reported Damages Based on Asset Type Chart -->
<script>
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawBasic);

    function drawBasic() {
      var data = google.visualization.arrayToDataTable([
        ['Asset Type', 'Frequency',],
        <?php
        $query = mysqli_query($conn, "SELECT tbl_assets.ASSET, COUNT(tbl_assets.ASSET) AS `value_occurrence` FROM tbl_assets, tbl_damagereports, tbl_inventory WHERE tbl_damagereports.ASSET_ID = tbl_inventory.SERIAL_NO AND tbl_inventory.CATEGORY = tbl_assets.ID GROUP BY tbl_assets.ASSET ORDER BY `value_occurrence` DESC LIMIT 10");
        while($row = mysqli_fetch_array($query)){
            echo "['" . $row["ASSET"] . "', " . number_format($row["value_occurrence"], 0, '.', '') . "],";
        }
        ?>
      ]);

      var options = {
        chartArea: {width: '70%', height: '100%'},
        hAxis: {
          title: 'No. of Reported Damages',
          titleTextStyle: {
                color: '#fff'
            },
            textStyle: {
                color: 'white'
            },
          minValue: 0
        },
        vAxis: {
          title: 'Asset Type',
          titleTextStyle: {
                color: '#fff'
            },
            textStyle: {
                color: 'white'
            }
        },
        backgroundColor: '#313131',
        legend: {
            position: 'none', 
        }
      };

      var chart = new google.visualization.BarChart(document.getElementById('damagesOnAssetTypes'));

      chart.draw(data, options);
    }
</script>

<!-- Most Frequent Damaged Asset Component Chart -->
<script>
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawBasic);

    function drawBasic() {
      var data = google.visualization.arrayToDataTable([
        ['Asset Type', 'Frequency',],
        <?php
        $query = mysqli_query($conn, "SELECT CONCAT(tbl_assets.ASSET, ' ', tbl_damagereports.PARTS) AS ASSET, COUNT(tbl_damagereports.PARTS) AS `value_occurrence` FROM tbl_assets, tbl_damagereports, tbl_inventory WHERE tbl_damagereports.ASSET_ID = tbl_inventory.SERIAL_NO AND tbl_inventory.CATEGORY = tbl_assets.ID GROUP BY tbl_assets.ASSET ORDER BY `value_occurrence` DESC LIMIT 10");
        while($row = mysqli_fetch_array($query)){
            echo "['" . $row["ASSET"] . "', " . number_format($row["value_occurrence"], 0, '.', '') . "],";
        }
        ?>
      ]);

      var options = {
        chartArea: {width: '70%', height: '100%'},
        hAxis: {
          title: 'No. of Reported Damages',
          titleTextStyle: {
                color: '#fff'
            },
            textStyle: {
                color: 'white'
            },
          minValue: 0
        },
        vAxis: {
          title: 'Asset Type',
          titleTextStyle: {
                color: '#fff'
            },
            textStyle: {
                color: 'white'
            }
        },
        backgroundColor: '#313131',
        legend: {
            position: 'none', 
        }
      };

      var chart = new google.visualization.BarChart(document.getElementById('frequentDamagedComponent'));

      chart.draw(data, options);
    }
</script>

<!-- Top 5 Departments with Most Damages Chart -->
<!-- NEED PA PALITAN YUNG VALUES -->
<script>
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawBasic);

    function drawBasic() {
      var data = google.visualization.arrayToDataTable([
        ['Department', 'Frequency',],
        <?php
        $query = mysqli_query($conn, "SELECT tbl_inventory.DEPARTMENT, COUNT(tbl_inventory.DEPARTMENT) AS `value_occurrence` FROM tbl_assets, tbl_damagereports, tbl_inventory WHERE tbl_damagereports.ASSET_ID = tbl_inventory.SERIAL_NO AND tbl_inventory.CATEGORY = tbl_assets.ID GROUP BY tbl_inventory.DEPARTMENT ORDER BY `value_occurrence` DESC LIMIT 5");
        while($row = mysqli_fetch_array($query)){
            echo "['" . $row["DEPARTMENT"] . "', " . number_format($row["value_occurrence"], 0, '.', '') . "],";
        }
        ?>
      ]);

      var options = {
        chartArea: {width: '70%', height: '100%'},
        hAxis: {
          title: 'Department with Most Damages',
          titleTextStyle: {
                color: '#fff'
            },
            textStyle: {
                color: 'white'
            },
          minValue: 0
        },
        vAxis: {
          title: 'Department',
          titleTextStyle: {
                color: '#fff'
            },
            textStyle: {
                color: 'white'
            }
        },
        backgroundColor: '#313131',
        legend: {
            position: 'none', 
        }
      };

      var chart = new google.visualization.BarChart(document.getElementById('damagesPerDepartment'));

      chart.draw(data, options);
    }
</script>

