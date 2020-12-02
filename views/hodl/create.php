<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\HodlPosition */

$this->title = 'Create Hodl Position';
$this->params['breadcrumbs'][] = ['label' => 'Hodl Positions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hodl-position-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
