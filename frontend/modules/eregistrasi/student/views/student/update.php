<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Student */

$this->title = 'Update Student: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Students', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view']];
$this->params['breadcrumbs'][] = 'Update';
$controller = $this->context;
$menus = $controller->module->getMenuItems();
$this->params['sideMenu'][$controller->module->uniqueId]=$menus;

echo \kartik\widgets\AlertBlock::widget([
    'useSessionFlash' => true,
    'type' => \kartik\widgets\AlertBlock::TYPE_ALERT
]);
?>
<div class="student-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
