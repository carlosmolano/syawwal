<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Dropdown;

/* @var $searchModel backend\models\TrainingHistorySearch */

$this->title = 'Training Histories';
$this->params['breadcrumbs'][] = $this->title;

$controller = $this->context;
$menus = $controller->module->getMenuItems();
$this->params['sideMenu'][$controller->module->uniqueId]=$menus;
?>
<div class="training-history-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            
				[
					'format' => 'html',
					'attribute' => 'revision',
					//'pageSummary' => 'Page Total',
					'vAlign'=>'middle',
					'headerOptions'=>['class'=>'kv-sticky-column'],
					'contentOptions'=>['class'=>'kv-sticky-column']
				],
            
				[
					'format' => 'html',
					'attribute' => 'name',
					//'pageSummary' => 'Page Total',
					'vAlign'=>'middle',
					'headerOptions'=>['class'=>'kv-sticky-column'],
					'contentOptions'=>['class'=>'kv-sticky-column'],
				],
            
				[
					'format' => 'html',
					'attribute' => 'start',
					//'pageSummary' => 'Page Total',
					'vAlign'=>'middle',
					'headerOptions'=>['class'=>'kv-sticky-column'],
					'contentOptions'=>['class'=>'kv-sticky-column'],
				],
            
				[
					'format' => 'html',
					'attribute' => 'finish',
					//'pageSummary' => 'Page Total',
					'vAlign'=>'middle',
					'headerOptions'=>['class'=>'kv-sticky-column'],
					'contentOptions'=>['class'=>'kv-sticky-column'],
				],
            
				[
					'format' => 'html',
					'attribute' => 'note',
					//'pageSummary' => 'Page Total',
					'vAlign'=>'middle',
					'headerOptions'=>['class'=>'kv-sticky-column'],
					'contentOptions'=>['class'=>'kv-sticky-column'],
				],
            
				[
					'format' => 'html',
					'attribute' => 'studentCount',
					//'pageSummary' => 'Page Total',
					'vAlign'=>'middle',
					'headerOptions'=>['class'=>'kv-sticky-column'],
					'contentOptions'=>['class'=>'kv-sticky-column'],
				],

				[
					'format' => 'html',
					'attribute' => 'created',
					//'pageSummary' => 'Page Total',
					'vAlign'=>'middle',
					'headerOptions'=>['class'=>'kv-sticky-column'],
					'contentOptions'=>['class'=>'kv-sticky-column'],
				],

            [
            	'class' => 'kartik\grid\ActionColumn',
            	'template' => '{view}'
            ],
        ],
		'panel' => [
			'heading'=>	Html::a('<i class="fa fa-fw fa-arrow-circle-left"></i> Back to the training list', Yii::$app->urlManager->createUrl(['bdk-execution/training/']), ['class' => 'btn btn-primary']),
			'showFooter'=>false
		],
		'responsive'=>true,
		'hover'=>true,
    ]); ?>
	<?php 	
	echo Html::beginTag('div', ['class'=>'row']);
		echo Html::beginTag('div', ['class'=>'col-md-2']);
			echo Html::beginTag('div', ['class'=>'dropdown']);
				echo Html::button('PHPExcel <span class="caret"></span></button>', 
					['type'=>'button', 'class'=>'btn btn-default', 'data-toggle'=>'dropdown']);
				echo Dropdown::widget([
					'items' => [
						['label' => 'EXport XLSX', 'url' => ['php-excel?filetype=xlsx&template=yes']],
						['label' => 'EXport XLS', 'url' => ['php-excel?filetype=xls&template=yes']],
						['label' => 'Export PDF', 'url' => ['php-excel?filetype=pdf&template=no']],
					],
				]); 
			echo Html::endTag('div');
		echo Html::endTag('div');
	
		echo Html::beginTag('div', ['class'=>'col-md-2']);
			echo Html::beginTag('div', ['class'=>'dropdown']);
				echo Html::button('OpenTBS <span class="caret"></span></button>', 
					['type'=>'button', 'class'=>'btn btn-default', 'data-toggle'=>'dropdown']);
				echo Dropdown::widget([
					'items' => [
						['label' => 'EXport DOCX', 'url' => ['open-tbs?filetype=docx']],
						['label' => 'EXport ODT', 'url' => ['open-tbs?filetype=odt']],
						['label' => 'EXport XLSX', 'url' => ['open-tbs?filetype=xlsx']],
					],
				]); 
			echo Html::endTag('div');
		echo Html::endTag('div');
		
	echo Html::endTag('div');
	?>

</div>