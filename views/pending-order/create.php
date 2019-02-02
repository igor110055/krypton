<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PendingOrder */

$this->title = 'Create Pending Order';
$this->params['breadcrumbs'][] = ['label' => 'Pending Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pending-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
