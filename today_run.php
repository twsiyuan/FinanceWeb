<?php require("config.php")?>
<!DOCTYPE html>
<html>
<head>
    <?php include("header.php")?>
    
    <style>
        @media only screen and (max-width: 480px) {
            .table tr td:nth-child(1) { display:none; visibility:hidden; }
            .table tr td:nth-child(5) { display:none; visibility:hidden; }
            
            .table tr th:nth-child(1) { display:none; visibility:hidden; }
            .table tr th:nth-child(5) { display:none; visibility:hidden; }
        }
        
        td.rtd {
            text-align: right; 
        }
    </style>
</head>
<body>
	<?php $active_page = "today_run";
    include("navigation.php")?>
    
	<div class="container theme-showcase" role="main">
		<div class="page-header">
			<h1>今日累積記錄 <a href="add.php"><i class="fa fa-plus"></i></a></h1>
		</div>
		<?php
        $conn = myUtf8Db($db_servername, $db_username, $db_password, $db_name);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        } 

        $timezone = getTimezone($conn);
        $stmt = $conn->prepare("SELECT `from`, `fromCurrency` FROM `record` WHERE `date` = DATE(DATE_SUB(NOW(), INTERVAL -? HOUR)) GROUP BY `from`, `fromCurrency` ORDER BY `createTime` ASC");
        $stmt->bind_param('i', $timezone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            echo "<p class=\"lead\">No Data</p>";
        }else{
            while($row = $result->fetch_assoc()) {
                $stmt2 = $conn->prepare("CALL `查詢 001 指定類別貨幣 歷史 n日內累計`(1 , ?, ?, ?)");
                $stmt2->bind_param('ssi', $row["from"], $row["fromCurrency"], $timezone);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                if (!$result2){
                    echo "Error:" . $stmt2->error . " <br/>";
                }else{
        ?>
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?=htmlspecialchars($row["from"])?></th>
                        <th><?=htmlspecialchars($row["fromCurrency"])?></th>
                        <th>Text</th>
                        <th>Location</th>
                        <th>Run</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    while($row2 = $result2->fetch_assoc()) {
                        ?>
                    <tr>
                        <td><?=$row2["id"]?></td>
                        <td><?=htmlspecialchars($row2["to"])?></td>
                        <td class="rtd"><?=rtrim(rtrim($row2["amount"], "0"), ".")?></td>
                        <td><?=htmlspecialchars($row2["text"])?></td>
                        <td><?=htmlspecialchars($row2["location"])?></td>
                        <td class="rtd"><?=rtrim(rtrim($row2["running_total"], "0"), ".")?></td>
                    </tr>
                    <?php
                        
                    }
                    ?>
                </tbody>
            </table>
            </div>
        <?php
                }
                $stmt2->close();
            }
        }

        $stmt->close();
        $conn->close();
        ?>
        
        <?php include("footer.php")?>
	</div>
</body>
</html>