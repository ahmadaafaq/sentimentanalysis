<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Cache-Control" content="no-cache">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="Lang" content="en">
        <link rel="shortcut icon" type="image/icon" href="favicon.ico"/>
        <title>Twitter Sentiment Analysis</title>
        <style>
            header {
                background-color: #666;
                padding: 10px;
                text-align: center;
                font-size: 35px;
                color: white;
            }
            .btn{
                padding: 2px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 12px;
                cursor: pointer;
            }
            .submit-btn{
                background-color: #4CAF50;
            }
            .reset-btn{
                background-color: #666;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th {
                background-color: #666;
                color: white;
            }
            .table-header {
                background-color: #ccc;
                text-align: center;
                font-size: 25px;
                color: white;
                padding: 10px;
                margin-top: 200px;
            }
            .form-data{
                float: left
            }
            .piechart{
                float: left;
                margin-left: 219px;
            }
        </style>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

        <script type="text/javascript">
            function resetData() {
                var pathArray = window.location.pathname.split('?');
                window.location.href = pathArray[0];
            }
        </script>
    </head>
    <body style="background-color: #1DA1F2">
        <header>
            Twitter Sentiment Analysis
        </header>
        <div>
            <div class="form-data">
                <h3>Type your keyword below to perform Sentiment Analysis on Twitter Results:</h3>
                <form method="GET">
                    <label>Keyword: </label> <input type="text" name="q" /> 
                    <input type="submit" class="btn submit-btn" />
                    <input type="reset" class="btn reset-btn" onclick="resetData()" />
                </form>
            </div>
            <div class="piechart" id="piechart"></div>
        </div>
        <?php
        if (isset($_GET['q']) && $_GET['q'] != '') {
            include_once(dirname(__FILE__) . '/config.php');
            include_once(dirname(__FILE__) . '/lib/TwitterSentimentAnalysis.php');

            $TwitterSentimentAnalysis = new TwitterSentimentAnalysis(DATUMBOX_API_KEY, TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_ACCESS_KEY, TWITTER_ACCESS_SECRET);

            //Search Tweets parameters as described at https://dev.twitter.com/docs/api/1.1/get/search/tweets
            $twitterSearchParams = array(
                'q' => $_GET['q'],
                'lang' => 'en',
                'count' => 15,
            );
            $results = $TwitterSentimentAnalysis->sentimentAnalysis($twitterSearchParams);

            $positive_count = $negative_count = $neutral_count = 0;
            foreach ($results as $tweet) {
                if ($tweet['sentiment'] == 'positive') {
                    $positive_count++;
                } else if ($tweet['sentiment'] == 'negative') {
                    $negative_count++;
                } else if ($tweet['sentiment'] == 'neutral') {
                    $neutral_count++;
                }
            }
            ?>
            <script type="text/javascript">
                // Load google charts
                google.charts.load('current', {'packages': ['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                // Draw the chart and set the chart values
                function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                        ['Task', 'Hours per Day'],
                        ['Positive', <?= $positive_count ?>],
                        ['Negative', <?= $negative_count ?>],
                        ['Neutral', <?= $neutral_count ?>],
                    ]);

                    // Optional; add a title and set the width and height of the chart
                    var options = {
                        'legend': 'left',
                        'title': 'Twitter Sentiment',
                        'is3D': true,
                        'width': 500,
                        'height': 200,
                        'colors': ['#00FF00', '#FF0000', '#ec8f6e']
                    };

                    // Display the chart inside the <div> element with id="piechart"
                    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                    chart.draw(data, options);
                }
            </script>
            <div class="table-header">
                Results for "<?php echo $_GET['q']; ?>"
            </div>
            <table border="1">
                <tr>
                    <th>Id</th>
                    <th>User</th>
                    <th>Tweet</th>
                    <th>Twitter Link</th>
                    <th>Sentiment</th>
                </tr>
                <?php
                foreach ($results as $tweet) {
                    $color = NULL;
                    if ($tweet['sentiment'] == 'positive') {
                        $color = '#00FF00';
                    } else if ($tweet['sentiment'] == 'negative') {
                        $color = '#FF0000';
                    } else if ($tweet['sentiment'] == 'neutral') {
                        $color = '#FFFFFF';
                    }
                    ?>
                    <tr style="background:<?php echo $color; ?>;">
                        <td><?php echo $tweet['id']; ?></td>
                        <td><?php echo $tweet['user']; ?></td>
                        <td><?php echo $tweet['text']; ?></td>
                        <td><a href="<?php echo $tweet['url']; ?>" target="_blank">View</a></td>
                        <td><?php echo $tweet['sentiment']; ?></td>
                    </tr>
                    <?php
                }
                ?>    
            </table>
            <?php
        }
        ?>
    </body>
</html>
