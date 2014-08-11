<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
/**
 * @var yii\web\View $this
 */

$this->title = 'Generate Routes';
$this->params['breadcrumbs'][] = ['label' => 'Routes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$controller = $this->context;
$menus = $controller->module->menus;
$this->params['sideMenu']=$menus;
?>
<?php $this->beginContent('@hscstudio/heart/views/layouts/column2module.php'); ?>

<?php
echo Html::beginForm();
echo GridView::widget([
	'dataProvider' => new ArrayDataProvider([
		'allModels' => $new,
			]),
	'columns' => [
		[
			'class' => 'yii\\grid\\CheckboxColumn',
			'checkboxOptions' => function($model){
				return [
					'value' => ArrayHelper::getValue($model, 'name'),
					'checked' => true,
				];
			},
		],
		[
			'class' => 'yii\\grid\\DataColumn',
			'attribute' => 'name',
		]
	]
]);
echo Html::submitButton('Append', ['name' => 'Submit','class' => 'btn btn-primary']);
echo Html::endForm();
?>
<?php $this->endContent();