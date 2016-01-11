<?php require("config.php")?>
<!DOCTYPE html>
<html>
<head>
    <?php include("header.php")?>
    
    <style>
        @media only screen and (max-width: 480px) {
            #main tr td:nth-child(1) { display:none; visibility:hidden; }
            #main tr th:nth-child(1) { display:none; visibility:hidden; }
        }
        
        td.rtd {
            text-align: right; 
        }
    </style>
</head>
<body>
	<?php include("navigation.php")?>
    
	<div class="container theme-showcase" role="main">
		<div class="page-header">
			<h1><?=(isset($page_title))? $page_title : "某日紀錄"?> <a href="add.php"><i class="fa fa-plus"></i></a></h1>
		</div>
		<?php
        if (!isset($day_offset)){
            $day_offset = 0;
        }
        if (!isset($day_range)){
            $day_range = 1;
        }

        if (!isset($allow_day_args) || $allow_day_args){
            if (isset($_GET["offset"])){
                $day_offset = intval($_GET["offset"]);
            }
        }
            
        $conn = myUtf8Db($db_servername, $db_username, $db_password, $db_name);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        } 

        $timezone = getTimezone($conn);
        $combines = array();
        $day_offset2 = $day_offset + $day_range;

        $stmt = $conn->prepare("SELECT `r`.*, `c1`.`type` AS `from_type`, `c2`.`type` AS `to_type` FROM (`record_detail` AS `r` LEFT JOIN `category` AS `c1` ON `r`.`from`=`c1`.`category`) LEFT JOIN `category` AS `c2` ON `r`.`to`=`c2`.`category` WHERE `r`.`date` >= DATE_SUB(DATE(DATE_SUB(NOW(), INTERVAL -? HOUR)), INTERVAL -? DAY) AND `r`.`date` < DATE_SUB(DATE(DATE_SUB(NOW(), INTERVAL -? HOUR)), INTERVAL -? DAY) ORDER BY `r`.`createTime` ASC");
        $stmt->bind_param('iiii', $timezone, $day_offset, $timezone, $day_offset2);
        $stmt->execute();
        $result = $stmt->get_result();
        $nodata = $result->num_rows <= 0;

        if ($nodata) {
            echo "<p class=\"lead\">No Data</p>";
        }else{
        ?>
        <div class="table-responsive">
        <table class="table table-striped" id="main">
            <thead>
                <tr>
                    <th>#</th>
                    <th>From</th>
                    <th>Amount</th>
                    <th>To</th>
                    <th>Text</th>
                    <th>Location</th>
                    <?php if ($day_range != 1){ echo "<th>Date</th>"; }?>
                </tr>
            </thead>
            <tbody>
            <?php
            while($row = $result->fetch_assoc()) {
            ?>
                <tr>
                    <td><?=$row["id"]?></td>
                    <td><?=htmlspecialchars($row["from"])?></td>
                    <td class="rtd"><?=rtrim(rtrim($row["fromAmount"], "0"), ".")?> <?=htmlspecialchars($row["fromCurrency"])?></td>
                    <td><?=htmlspecialchars($row["to"])?></td>
                    <td><?=htmlspecialchars($row["text"])?></td>
                    <td><?=htmlspecialchars($row["location"])?></td>
                    <?php if ($day_range != 1){ echo "<td>" . $row["date"] . "</td>"; }?>
                </tr>
            <?php
                if ($row["from_type"] == 0 || $row["from_type"] == 1){
                    $exist = false;
                    foreach ($combines as $itr) {
                        if ($itr->category == $row["from"] && $itr->currency == $row["fromCurrency"]){
                            $exist = true;
                            break;
                        }
                    }
                    if (!$exist){
                        $combine = (object) ['category' => $row["from"], 'currency' => $row["fromCurrency"]];
                        array_push($combines, $combine);
                    }
                }
                
                if ($row["to_type"] == 0 || $row["to_type"] == 1){
                    $exist = false;
                    foreach ($combines as $itr) {
                        if ($itr->category == $row["to"] && $itr->currency == $row["toCurrency"]){
                            $exist = true;
                            break;
                        }
                    }
                    if (!$exist){
                        $combine = (object) ['category' => $row["to"], 'currency' => $row["toCurrency"]];
                        array_push($combines, $combine);
                    }
                }
            }
            ?>
            </tbody>
        </table>
        </div>
        <?php
        }
        $stmt->close();
        ?>

        <?php
        if (count($combines > 0) && !$nodata){
        ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Current</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($combines as $itr){
                $stmt = $conn->prepare("SELECT SUM(`amount`) AS `sum` FROM `record_detail_flow` WHERE `category`=? AND `currency`=? AND `date` < DATE_SUB(DATE(DATE_SUB(NOW(), INTERVAL -? HOUR)), INTERVAL -? DAY)");
                $stmt->bind_param('ssii', $itr->category, $itr->currency, $timezone, $day_offset2);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
            ?>
                <tr>
                    <td><?=htmlspecialchars($itr->category)?></td>
                    <td><?=htmlspecialchars(number_format($row["sum"], 2) . " " . htmlspecialchars($itr->currency))?></td>
                </tr>
            <?php
                $stmt->close();
            }
            ?>
            </tbody>
        </table>
        <?php
        }
        ?>
        <?php
        $conn->close();
        ?>
        
        <?php include("footer.php")?>
	</div>
</body>
</html>