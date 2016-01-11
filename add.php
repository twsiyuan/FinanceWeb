<?php 
require("config.php");
    
$conn = myUtf8Db($db_servername, $db_username, $db_password, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} 

?>
<!DOCTYPE html>
<html>
<head>
    <?php include("header.php")?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/3.1.1/bootstrap3-typeahead.min.js"></script>
    <style>
        .input-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
	<?php 
    $active_page = "add";
    include("navigation.php")?>
    
	<div class="container theme-showcase" role="main">
		<div class="page-header">
			<h1>新增記錄</h1>
		</div>
		<div id="message">
        <?php
        if (isset($_SESSION["add_alert"])){
            echo $_SESSION["add_alert"];
            unset($_SESSION["add_alert"]);
        }
        ?>
		</div>
        <?php
        echo "<script>";
        $stmt = $conn->prepare("SELECT DISTINCT `from` AS `category` FROM `record`;");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0){
            echo "var from_cates = [";
            while($row = $result->fetch_assoc()){
                echo "'" . htmlspecialchars($row['category']) . "',";
            }
            echo "];";
        }
        $stmt->close();
        
        $stmt = $conn->prepare("SELECT DISTINCT `to` AS `category` FROM `record`;");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0){
            echo "var to_cates = [";
            while($row = $result->fetch_assoc()){
                echo "'" . htmlspecialchars($row['category']) . "',";
            }
            echo "];";
        }
        $stmt->close();
        echo "</script>";
        ?>
        <form method="post" id="main_form">
            <div class="input-group">
                <span class="input-group-addon" id="from_addon">From</span>
                <input type="text" class="form-control" id="from" name="from" value="<?=htmlspecialchars(getConfigValue($conn, 'add_from', '現金-錢包'))?>" aria-describedby="from_addon" data-provide="typeahead" autocomplete="off">
                <script>
                    $("#from").typeahead({ 
                        hint: true, 
                        highlight: true, 
                        minLength: 1, 
                        source:from_cates,
                        afterSelect:function(query){
                            $("#from").trigger('change');
                        },
                    });
                    
                    $("#from").change(function(){
                        $("#from").attr("hasChanged", true);
                    });
                </script>
                <div class="input-group-btn">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown"><span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="javascript:$('#from').val('現金-錢包');$('#from').trigger('change');"><i class="fa fa-money fa-lg"></i> 現金-錢包</a></li>
                        <li><a class="dropdown-item" href="javascript:$('#from').val('悠悠卡');$('#from').trigger('change');"><i class="fa fa-credit-card fa-lg"></i> 悠悠卡</a></li>
                        <li><a class="dropdown-item" href="javascript:$('#from').val('兆豐銀行聯名卡');$('#from').trigger('change');"><i class="fa fa-credit-card-alt fa-lg"></i> 兆豐銀行聯名卡</a></li>
                        <?php
                        $stmt = $conn->prepare("SELECT DISTINCT `from` FROM `record` GROUP BY `from` ORDER BY MAX(`date`) DESC, MAX(`createTime`) DESC LIMIT 11;");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0){
                            echo '<li class="divider"></li>';
                            while($row = $result->fetch_assoc()){
                                echo '<li><a href="javascript:$(\'#from\').val(\'' . htmlspecialchars($row['from']) . '\');$(\'#from\').trigger(\'change\');">' . $row['from'] . '</a></li>';
                            }
                        }
                        $stmt->close();
                        ?>
                    </ul>
                </div>
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="to_addon">To</span>
                <input type="text" class="form-control" id="to" name="to" value="<?=htmlspecialchars(getConfigValue($conn, 'add_to', '現金-錢包'))?>" aria-describedby="to_addon" data-provide="typeahead" autocomplete="off">
                <script>
                    $("#to").typeahead({ 
                        hint: true, 
                        highlight: true, 
                        minLength: 1, 
                        source:to_cates,
                        afterSelect:function(query){
                            $("#to").trigger('change');
                        },
                    });
                    
                    $("#to").change(function(){
                        $("#to").attr("hasChanged", true);
                    });
                </script>
                <div class="input-group-btn">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown"><span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="javascript:$('#to').val('交通費');$('#to').trigger('change');"><i class="fa fa-train fa-lg"></i> 交通費</a></li>
                        <li><a class="dropdown-item" href="javascript:$('#to').val('早餐');$('#to').trigger('change');"><i class="fa fa-cutlery fa-lg"></i> 早餐</a></li>
                        <li><a class="dropdown-item" href="javascript:$('#to').val('午餐');$('#to').trigger('change');"><i class="fa fa-cutlery fa-lg"></i> 午餐</a></li>
                        <li><a class="dropdown-item" href="javascript:$('#to').val('下午茶');$('#to').trigger('change');"><i class="fa fa-cutlery fa-lg"></i> 下午茶</a>  </li>
                        <li><a class="dropdown-item" href="javascript:$('#to').val('晚餐');$('#to').trigger('change');"><i class="fa fa-glass fa-lg"></i> 晚餐</a></li>
                        <li><a class="dropdown-item" href="javascript:$('#to').val('點心');$('#to').trigger('change');"><i class="fa fa-meh-o fa-lg"></i> 點心</a></li>
                        <?php
                        $stmt = $conn->prepare("SELECT DISTINCT `to` FROM `record` GROUP BY `to` ORDER BY MAX(`date`) DESC, MAX(`createTime`) DESC LIMIT 8;");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0){
                            echo '<li class="divider"></li>';
                            while($row = $result->fetch_assoc()){
                                echo '<li><a href="javascript:$(\'#to\').val(\'' . htmlspecialchars($row['to']) . '\');$(\'#to\').trigger(\'change\');">' . $row['to'] . '</a></li>';
                            }
                        }
                        $stmt->close();
                        ?>
                    </ul>
                </div>
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="from_currency_addon">Currency</span>
                <?php $currencies = getCurrencies($conn);?>
                <select class="form-control" id="from_currency" name="from_currency" aria-describedby="from_currency_addon">
                <?php
                $from_currency = getConfigValue($conn, 'add_from_currency', 'TWD');
                foreach ($currencies as $option){
                    $selected = $option->value == $from_currency ? 'selected="true"' : '';
                    echo '<option value="' . $option->value . '" ' . $selected . '>' . $option->text . '</option>';
                }
                ?>
                </select>
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="from_amount_addon">Amount</span>
                <input type="number" min = "0" class="form-control" id="from_amount" name="from_amount" value="0" aria-describedby="from_amount_addon" autocomplete="off">
                <script>
                    $("#from_amount").change(function(){
                        $("#from_amount").attr("hasChanged", true);
                    });
                </script>
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="text_addon">Text</span>
                <input type="text" class="form-control typeahead" id="text" name="text" aria-describedby="text_addon" placeholder="What happened?" data-provide="typeahead" autocomplete="off">
                <script>
                    $('#text').typeahead({ 
                        source: function(query, proccess){
                            $.ajax({
                                url: "typeahead.php?t=text&q=" + encodeURI(query), 
                                success: function(result){ proccess(result); },
                            });
                        },
                        afterSelect: function(query){
                            $.ajax({
                                url: "typeahead.php?t=recent&q=" + encodeURI(query), 
                                success: function(result){ 
                                    if (result){
                                        if (!$('#from').attr("hasChanged") && result.from){
                                            $('#from').val(result.from);
                                        }
                                        if (!$('#to').attr("hasChanged") && result.to){
                                            $('#to').val(result.to);
                                        }
                                        if (!$('#from_amount').attr("hasChanged") && result.fromAmount){
                                            $('#from_amount').val(result.fromAmount);
                                        }
                                        if (!$('#location').attr("hasChanged") && result.location){
                                            $('#location').val(result.location);
                                        }
                                    }
                                },
                            });
                        },
                    });
                </script>
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="location_addon">Location</span>
                <input type="text" class="form-control" id="location" name="location" aria-describedby="location_addon" placeholder="Where were it?" autocomplete="off">
                <script>
                    $('#location').typeahead({ 
                        source: function(query, proccess){
                            $.ajax({
                                url: "typeahead.php?t=location&q=" + encodeURI(query), 
                                success: function(result){ proccess(result); },
                            });
                        },
                    });

                    $("#location").change(function(){
                        $("#location").attr("hasChanged", true);
                    });
                </script>
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="date_addon">Date</span>
                <?php
                $tz = new DateTimeZone('Europe/London');
                $date = new DateTime(null, $tz);
                $date->modify('+' . getTimezone($conn) . ' hours');
                ?>
                <input type="date" class="form-control" id="date" name="date" value="<?=$date->format('Y-m-d')?>" aria-describedby="date_addon" autocomplete="off">
            </div>
            <input type="button" id="btn_submit" class="btn btn-default" value="新增">
			<script>
				$("#btn_submit").click(function(){
					$("#btn_submit").attr("disabled", "true");

					jQuery.ajax({
                        type: "POST",
						url: "data/record.php",
						processData: false,
						contentType: false,
						data: JSON.stringify(
                            {
                                "from" : $("#from").val(),
                                "fromAmount" : $("#from_amount").val(),
                                "fromCurrency" : $("#from_currency").val(),
                                "to" : $("#to").val(),
                                "text" : $("#text").val(),
                                "location": $("#location").val(),
                                "date" : $("#date").val(),
                            }
                        ),
                        dataType: "json",
						success: function (response) {		
                            $("#from").removeAttr("hasChanged");
                            $("#to").removeAttr("hasChanged");
                            $("#from_amount").removeAttr("hasChanged");
                            $("#location").removeAttr("hasChanged");

                            $("#message").html('<div class="alert alert-success" role="alert"><strong>完成！</strong>新增記錄 ' + response.id + '。</div>');

							$("#btn_submit").removeAttr("disabled");
						},
						error: function(jqXHR, status){
							$("#btn_submit").removeAttr("disabled");
							$("#message").html('<div class="alert alert-danger" role="alert"><strong>錯誤！</strong>HTTP ' + jqXHR.status + ' (' + jqXHR.statusText + ')<div>' + jqXHR.responseText + '</div>。</div>');
						}
					});
				});
			</script>
        </form>
        <?php include("footer.php")?>
	</div>
</body>
</html>
<?php $conn->close();?>