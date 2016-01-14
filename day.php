<?php require("config.php")?>
<!DOCTYPE html>
<html>
<head>
    <?php include("header.php")?>
    
    <style>
        @media only screen and (max-width: 480px) {
            .col_id { display:none; visibility:hidden; }
            .col_id { display:none; visibility:hidden; }
			
			.col_last_edit { display:block !important; visibility:visible !important; }
			.col_last_edit { display:block !important; visibility:visible !important; }
        }
		
		.col_last_edit { display:none; visibility:hidden; }
        .col_last_edit { display:none; visibility:hidden; }
        
        .col_date { display:none; visibility:hidden; }

        td.col_amount {
            text-align: right; 
        }
    </style>
</head>
<body>
	<?php include("navigation.php")?>
    <article class="container theme-showcase" role="main">
		<div class="page-header">
			<h1><?=(isset($page_title))? $page_title : "某日紀錄"?> <a href="record.php"><i class="fa fa-plus"></i></a></h1>
		</div>
        <div class="row">
            <div class="col-lg-9">
                <div class="table-responsive">
                    <table class="table table-striped" id="records">
                        <thead>
                            <tr>
                                <th class="col_edit col_first_edit"></th>
                                <th class="col_id">#</th>
                                <th>From</th>
                                <th class="col_amount">Amount</th>
                                <th>To</th>
                                <th>Text</th>
                                <th>Location</th>
                                <th class="col_date">Date</th>
                                <th class="col_edit col_last_edit"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-3">
                <table class="table table-striped" id="assets">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Current</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            function numberWithCommas(x) {
                var parts = x.toString().split(".");
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                return parts.join(".");
            }

            var interest = [];
            function add_interest(category, currency){
                if (!test_interest(category, currency)){
                    interest.push({'category': category, 'currency': currency,}); 
                }
            }
            
            function test_interest(category, currency){
                var exist = false;
                $.each(interest, function(index, record){
                    if (!exist){
                        exist = record.category == category && record.currency == currency;
                    }
                });
                return exist;
            }
            
            var date = new Date();
            var date_str = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
            $.ajax({
                url: "data/records.php?start_date=" + date_str, 
                success: function(result){ 
                    var ttemp = $('#records thead tr:first-child');
                    var tbody = $('#records tbody');
                    
                    
                    $.each(result, function(index, record){
                        add_interest(record.from, record.fromCurrency);
                        add_interest((record.to)? record.to : record.from, (record.toCurrency)? record.toCurrency : record.fromCurrency);

                        var edit1 = $('<a></a>').attr('href', 'record.php?id=' + record.id).append($('<i class="fa fa-pencil-square-o"></i>'));
                        var edit2 = $('<a></a>').attr('href', 'record.php?id=' + record.id).append($('<i class="fa fa-pencil-square-o"></i>'));
                        
                        var tr = $('<tr></tr>');
                        tr.append($('<td></td>').append(edit1));
                        tr.append($('<td></td>').text(record.id));
                        tr.append($('<td></td>').text(record.from));
                        tr.append($('<td></td>').text(numberWithCommas(record.fromAmount) + ' ' + record.fromCurrency));
                        tr.append($('<td></td>').text(record.to));
                        tr.append($('<td></td>').text(record.text));
                        tr.append($('<td></td>').text(record.location));
                        tr.append($('<td></td>').text(record.date));
                        tr.append($('<td></td>').append(edit2));
                        
                        ttemp.children('th').each(function (index, th) {
                            $(tr.children().get(index)).addClass($(th).attr('class'));
                        });
                        
                        tbody.append(tr);
                    });
                    
                    $.ajax({
                        url: "data/assets.php?date=" + date_str, 
                        success: function(result){ 
                            var ttemp = $('#assets thead tr:first-child');
                            var tbody = $('#assets tbody');
                            $.each(result, function(index, record){
                                if (test_interest(record.category, record.currency)){
                                    var tr = $('<tr></tr>');
                                    tr.append($('<td></td>').text(record.category));
                                    tr.append($('<td></td>').text(numberWithCommas(record.amount) + ' ' + record.currency));
                                    ttemp.children('th').each(function (index, th) {
                                        $(tr.children().get(index)).addClass($(th).attr('class'));
                                    });

                                    tbody.append(tr);
                                }
                            });
                        },
                    });
                },
            });
        </script>
    </article>
    <?php include("footer.php")?>
</body>
</html>