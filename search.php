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
	<?php $active_page = "search";
    include("navigation.php")?>
    
	<div class="container theme-showcase" role="main">
		<div class="page-header">
			<h1>搜尋資料</h1>
		</div>
        <div class="row">
            <form method="post" class="form-inline">
            <div class="form-group col-xs-6">
                <select id="field" name="field" class="form-control input-group-lg">
                    <option value="*">任意位置</option>
                    <option value="id">Id</option>
                    <option value="from">From</option>
                    <option value="to">To</option>
                    <option value="text">Text</option>
                    <option value="location">Location</option>
                    <option value="date">Date</option>
                </select>
                <select id="condition" name="condition" class="form-control input-group-lg">
                    <option value="=">=</option>
                    <option value="<">&lt;</option>
                    <option value=">">&gt;</option>
                    <option value="<=">&lt;=</option>
                    <option value=">=">&gt;=</option>
                    <option value="!=">!=</option>
                    <option value="LIKE">LIKE</option>
                    <option value="LIKE %%">LIKE %%</option>
                </select>
                <input type="text" class="form-control input-group-lg" id="keyword" name="keyword" placeholder="Keyword">
                <script>
                if(typeof(Storage) !== "undefined") {
                    $("#field").change(function(){
                        localStorage.setItem("field", $("#field").val());
                    });

                    $("#condition").change(function(){
                        localStorage.setItem("condition", $("#condition").val());
                    });
                    
                    $("#keyword").change(function(){
                        localStorage.setItem("keyword", $("#keyword").val());
                    });
                    
                    $("#field").val( localStorage.getItem("field") );
                    $("#condition").val( localStorage.getItem("condition") );
                    $("#keyword").val( localStorage.getItem("keyword") );
                }
                </script>
            </div>
            </form>
        </div>
		<?php
        $conn = myUtf8Db($db_servername, $db_username, $db_password, $db_name);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        $field = isset($_POST["field"])? $_POST["field"] : null;
        $condition = isset($_POST["condition"])? $_POST["condition"] : null;
        $keyword = isset($_POST["keyword"])? $_POST["keyword"] : "";

        switch($field){
            case "*":
            case "id":
            case "from":
            case "to":
            case "text":
            case "location":
            case "date":
                break;
            default:
                $field = null;
                break;
        }

        switch($condition){
            case "=":
            case "<":
            case ">":
            case "<=":
            case ">=":
            case "!=":
            case "LIKE":
                break;
            case "LIKE %%":
                $condition = "LIKE";
                $keyword = "%" . $keyword . "%";
                break;
            default:
                $condition = null;
                break;
        }

        $sql = "SELECT * FROM `record_detail` AS `r`";
        $stmt = null;
        if ($field != null && $condition != null){
            if ($field == "*"){
                $sql = $sql . " WHERE `id` " . $condition . " ?";
                $sql = $sql . " OR `from` " . $condition . " ?";
                $sql = $sql . " OR `to` " . $condition . " ?";
                $sql = $sql . " OR `text` " . $condition . " ?";
                $sql = $sql . " OR `location` " . $condition . " ?";
                $sql = $sql . " OR `date` " . $condition . " ?";
            }else{
                $sql = $sql . " WHERE `" . $field . "` " . $condition . " ?";
            }

            $stmt = $conn->prepare($sql . " ORDER BY `date` DESC, `createTime` DESC LIMIT 50;");

            if ($field != null && $condition != null){
                if ($field == "*"){
                    $stmt->bind_param('ssssss', $keyword, $keyword, $keyword, $keyword, $keyword, $keyword);
                }else{
                    $stmt->bind_param('s', $keyword);
                }
            }

            $stmt->execute();
            $result = $stmt->get_result();
        ?><br/>
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
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()) {?>
                    <tr>
                        <td><?=htmlspecialchars($row["id"])?></td>
                        <td><?=htmlspecialchars($row["from"])?></td>
                        <td><?=htmlspecialchars(number_format($row["fromAmount"], 2) . " " . $row["fromCurrency"])?></td>
                        <td><?=htmlspecialchars($row["to"])?></td>
                        <td><?=htmlspecialchars($row["text"])?></td>
                        <td><?=htmlspecialchars($row["location"])?></td>
                        <td><?=htmlspecialchars($row["date"])?></td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
            </div>
        <?php
            $stmt->close();
        }

        $conn->close();
        ?>
        
        <?php include("footer.php")?>
	</div>
</body>
</html>