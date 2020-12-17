<?php

/* @var $this yii\web\View */
/* @var $dataset array */
/* @var $result */

$this->title = 'Krypton';
?>
<div class="site-index">

    <div class="body-content">
        <div class="row">
            <h1>Dashboard</h1>

        </div>
        <div style="width:100%;">
            <canvas id="myChart"></canvas>
        </div>
        <pre>
            <?php print_r($result) ?>
        </pre>

    </div>
</div>
<?php
$labels = implode(',', $dataset['days']);
$values = implode(',', $dataset['values']);
$script = <<< JS
var ctx = $('#myChart');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [$labels],
        datasets: [{
            label: 'Profit chart',
            data: [$values],
        }]
    },
    options: {
        responsive: true,
        scales: {
            xAxes: [{
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'Day'
                }
            }],
            yAxes: [{
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'Value'
                },
                ticks: {
                    suggestedMin: 1000,
                    suggestedMax: 20000
                }
            }]
        }
    }
});
JS;
$position = \yii\web\View::POS_READY;
$this->registerJs($script, $position);