<?php 
require("config.php");

$edit = isset($_GET["id"]);
    
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
	if (!$edit){
		$active_page = "add";
	}
    include("navigation.php")?>
    
	<div class="container theme-showcase" role="main">
		<div class="page-header">
			<h1><?=($edit)? "修改" : "新增"?>記錄</h1>
		</div>
        <form method="post" id="main_form">
			<?php if ($edit){?>
			 <div class="input-group">
                <span class="input-group-addon" id="id_addon">Id</span>
                <input type="number" class="form-control" id="id" name="id" value="<?=htmlspecialchars($_GET["id"])?>" aria-describedby="id_addon" readonly="readonly">
            </div>
			<?php }?>
            <div class="input-group">
                <span class="input-group-addon" id="from_addon">From</span>
                <input type="text" class="form-control" id="from" name="from" value="<?=htmlspecialchars(getConfigValue($conn, 'add_from', '現金-錢包'))?>" aria-describedby="from_addon" data-provide="typeahead" autocomplete="off">
                <script>
                    $("#from").typeahead({ 
                        hint: true, 
                        highlight: true, 
                        minLength: 1, 
                        source: function(query, proccess){
                            $.ajax({
                                url: "data/from-category.php", 
                                success: function(result){ proccess(result); },
                            });
                        },
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
                    <ul class="dropdown-menu dropdown-menu-right" id="from_fav_category"></ul>
					<script>
						function from_fav_click(value) {
							$('#from').val(value).trigger('change');
                        }
						
						$.ajax({
						url: "data/from-fav-category.php", 
						success: function(result){ 
							$.each(result, function(index, value){
								var item = $('<li></li>');
								var link = $('<a class="dropdown-item" href="javascript:from_fav_click(\'' +  encodeURI(value.name) + '\');"></a>');
								if (value.icon) {
                                    link.html('<i class="fa fa-lg ' + value.icon + '"></i> ' + value.name);
                                }else{
									link.text(value.name);
								}

								$("#from_fav_category").append(item.append(link));
							});
						},
					});
					</script>
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
                        source: function(query, proccess){
                            $.ajax({
                                url: "data/to-category.php", 
                                success: function(result){ proccess(result); },
                            });
                        },
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
                    <ul class="dropdown-menu dropdown-menu-right" id="to_fav_category"></ul>
					<script>
						function to_fav_click(value) {
							$('#to').val(value).trigger('change');
                        }
						
						$.ajax({
						url: "data/to-fav-category.php", 
						success: function(result){ 
							$.each(result, function(index, value){
								var item = $('<li></li>');
								var link = $('<a class="dropdown-item" href="javascript:to_fav_click(\'' +  encodeURI(value.name) + '\');"></a>');
								if (value.icon) {
                                    link.html('<i class="fa fa-lg ' + value.icon + '"></i> ' + value.name);
                                }else{
									link.text(value.name);
								}

								$("#to_fav_category").append(item.append(link));
							});
						},
					});
					</script>
                </div>
            </div>
            <div class="input-group">
                <span class="input-group-addon" id="from_currency_addon">Currency</span>
                <select class="form-control" id="from_currency" name="from_currency" aria-describedby="from_currency_addon"></select>
				<script>
				var default_currency = "<?=getConfigValue($conn, 'add_from_currency', 'TWD')?>";
				$.ajax({
					url: "data/currency.php", 
					success: function(result){ 
						$.each(result, function(index, value){
							var option = $("<option></option>").attr("value", value.code).text(value.code);
							if (default_currency == value.code){
								option.attr("selected", true);
							}
							$("#from_currency").append(option);
						});
					},
				});
				</script>
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
                <input type="date" class="form-control" id="date" name="date" aria-describedby="date_addon" autocomplete="off">
				<script>
				$.ajax({
					url: "data/date.php", 
					success: function(result){ $("#date").val(result.current_date); },
				});
				</script>
			</div>
			<div>
				<input type="button" id="btn_submit" class="btn btn-default" value="<?=($edit)? "修改" : "新增"?>">
				<span id="message"></span>
			</div>
			<script>
				$("#btn_submit").click(function(){
					$("#btn_submit").attr("disabled", "true");

					jQuery.ajax({
                        type: "<?=($edit)? "PUT" : "POST"?>",
						url: "data/record.php",
						processData: false,
						contentType: false,
						data: JSON.stringify(
                            {
								"id": $("#id").val(),
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

							<?php if ($edit){?>
							var msgItem = $('<span class="alert alert-success" role="alert"><strong>完成！</strong>修改記錄 <?=$_GET['id']?>。</span>')
							<?php }else{?>
                            var msgItem = $('<span class="alert alert-success" role="alert"><strong>完成！</strong>新增記錄 ' + response.id + '。</span>');
							<?php }?>
							$("#message").append(msgItem.delay(3200).fadeOut());
							
							$("#btn_submit").removeAttr("disabled");
						},
						error: function(jqXHR, status){
							$("#btn_submit").removeAttr("disabled");
							$("#message").html('<div class="alert alert-danger" role="alert"><strong>錯誤！</strong>HTTP ' + jqXHR.status + ' (' + jqXHR.statusText + ')<div>' + jqXHR.responseText + '</div>。</div>');
						}
					});
				});
			</script>
			<?php if ($edit){?>
			<script>
				$.ajax({
					url: "data/record.php?id=<?=urlencode($_GET["id"])?>", 
					success: function(result){
						$("#from").val(result.from);
						$("#from_amount").val(result.fromAmount);
						$("#from_currency").val(result.fromCurrency);
						$("#to").val(result.to);
						$("#text").val(result.text != null ? result.text : "NULL");
						$("#location").val(result.location != null ? result.location : "NULL");
						$("#date").val(result.date);
					},
				});
			</script>
			<?php }?>
        </form>
        <?php include("footer.php")?>
	</div>
</body>
</html>
<?php $conn->close();?>