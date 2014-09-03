<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\bootstrap\Dropdown;
use kartik\widgets\Select2;
use backend\models\ActivityRoom;
use kartik\widgets\DepDrop;

/* @var $searchModel backend\models\TrainingSearch */

$this->title = 'Trainings';
$this->params['breadcrumbs'][] = $this->title;

$controller = $this->context;
$menus = $controller->module->getMenuItems();
$this->params['sideMenu'][$controller->module->uniqueId]=$menus;
?>
<div class="training-index">

	<?php \yii\widgets\Pjax::begin([
		'id'=>'pjax-training-gridview',
	]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            	['class' => 'kartik\grid\SerialColumn'],
                        
				[
					'class' => 'kartik\grid\EditableColumn',
					'attribute' => 'name',
					'vAlign'=>'middle',
					'hAlign' => 'left',
					'headerOptions'=>['class'=>'kv-sticky-column'],
					'contentOptions'=>['class'=>'kv-sticky-column'],
					'editableOptions'=>['header'=>'Name', 'size'=>'md','formOptions'=>['action'=>\yii\helpers\Url::to('editable')]]
				],
            
				[
					'attribute' => 'start',
					'vAlign'=>'middle',
					'headerOptions'=>['class'=>'kv-sticky-column'],
					'contentOptions'=>['class'=>'kv-sticky-column'],
					'value' => function ($data)
					{
						return date('d F Y', strtotime($data->start));
					}
				],
            
				[
					'attribute' => 'finish',
					'vAlign'=>'middle',
					'headerOptions'=>['class'=>'kv-sticky-column'],
					'contentOptions'=>['class'=>'kv-sticky-column'],
					'value' => function ($data)
					{
						return date('d F Y', strtotime($data->finish));
					}
				],
            
				[
					'class' => 'kartik\grid\EditableColumn',
					'attribute' => 'classCount',
					'vAlign'=>'middle',
					'hAlign' => 'center',
					'headerOptions'=>['class'=>'kv-sticky-column'],
					'contentOptions'=>['class'=>'kv-sticky-column'],
					'editableOptions'=>['header'=>'Class Count', 'size'=>'md','formOptions'=>['action'=>\yii\helpers\Url::to('editable')]]
				],
            
				[
					'label' => 'Student Count',
					'class' => 'kartik\grid\EditableColumn',
					'attribute' => 'studentCount',
					'vAlign'=>'middle',
					'hAlign'=>'center',
					'headerOptions'=>['class'=>'kv-sticky-column'],
					'contentOptions'=>['class'=>'kv-sticky-column'],
					'editableOptions'=>['header'=>'Student Count', 'size'=>'md','formOptions'=>['action'=>\yii\helpers\Url::to('editable')]]
				],

				[
					'format' => 'raw',
					'vAlign'=>'middle',
					'hAlign'=>'center',
					'headerOptions'=>['class'=>'kv-sticky-column'],
					'contentOptions'=>['class'=>'kv-sticky-column'],
					'label' => 'Room',
					'value' => function ($data)
					{
						$roomCount = ActivityRoom::find()->where(['type' => 0, 'activity_id' => $data->id])->count();

						$roomWaitingCount = ActivityRoom::find()->where([
											'type' => 0, 
											'activity_id' => $data->id, 
											'status' => 0
										])->count();
						$roomProcessCount = ActivityRoom::find()->where([
											'type' => 0, 
											'activity_id' => $data->id, 
											'status' => 1
										])->count();
						$roomApprovedCount = ActivityRoom::find()->where([
											'type' => 0, 
											'activity_id' => $data->id, 
											'status' => 2
										])->count();
						$roomRejectedCount = ActivityRoom::find()->where([
											'type' => 0, 
											'activity_id' => $data->id, 
											'status' => 3
										])->count();
						
						$fOut = '<div class="col-md-3">
									<div class="label label-warning" data-toggle="tooltip" data-placement="top" title="Waiting...">
									'.$roomWaitingCount.'
									</div>
								</div>';
						$fOut .= '<div class="col-md-3">
									<div class="label label-info" data-toggle="tooltip" data-placement="top" title="Process...">
									'.$roomProcessCount.'
									</div>
								</div>';
						$fOut .= '<div class="col-md-3">
									<div class="label label-success" data-toggle="tooltip" data-placement="top" title="Approved!">
									'.$roomApprovedCount.'
									</div>
								</div>';
						$fOut .= '<div class="col-md-3">
									<div class="label label-danger" data-toggle="tooltip" data-placement="top" title="Rejected!">
									'.$roomRejectedCount.'
									</div>
								</div>';

						$fOut .= '<div class="col-md-12">
									<a class="label label-default" href="'.Url::to(['training-room/index', 'tb_training_id' => $data->id]).'">
									'.$roomCount.' | Add Room <i class="fa fa-fw fa-play"></i>
									</a>
								</div>';

						return $fOut;
					}
				],

				[
					'format' => 'html',
					'vAlign'=>'middle',
					'hAlign'=>'center',
					'label' => 'Status',
					'value' => function ($data)
					{
						if ($data->approvedStatus === null)
						{
							return '<div class="label label-warning" data-toggle="tooltip" data-placement="top" title="Waiting for approval..."><i class="fa fa-fw fa-spinner fa-spin"></i></div>';
						}
						elseif ($data->approvedStatus === 0) {
							return '<div class="label label-danger" data-toggle="tooltip" data-placement="top" title="Rejected!"><i class="fa fa-fw fa-times"></i></div>';
						}
						else
						{
							return '<div class="label label-success" data-toggle="tooltip" data-placement="top" title="Approved!"><i class="fa fa-fw fa-check"></i></div>';
						}
					}
				],
				[
					'format' => 'html',
					'vAlign'=>'middle',
					'hAlign'=>'center',
					'label' => 'Revision',
					'value' => function ($data) {
						$countRevision = \backend\models\TrainingHistory::find()
									->where(['tb_training_id' => $data->id,])
									->count()-1;
						if($countRevision>0){
							return Html::a($countRevision.' x', ['training-history/','tb_training_id'=>$data->id], ['class' => 'label label-danger']);
						}
						else{
							return '<span class="label label-danger">0</span>';
						}
					}
				],

            ['class' => 'kartik\grid\ActionColumn'],
        ],
		'panel' => [
			'heading'=>'<h3 class="panel-title"><i class="fa fa-fw fa-globe"></i> Training List</h3>',
			'before'=>Html::a('<i class="fa fa-fw fa-plus"></i> Create Training', ['create'], ['class' => 'btn btn-success']). ' '.
				'<div class="pull-right" style="margin-right:5px;">'.
				Select2::widget([
					'name' => 'status', 
					'data' => ['1'=>'Published','0'=>'Unpublished','all'=>'Show All'],
					'value' => $status,
					'options' => [
						'placeholder' => 'Status ...', 
						'class'=>'form-control', 
						'onchange'=>'
							$.pjax.reload({url: "'.\yii\helpers\Url::to(['/'.$controller->module->uniqueId.'/training/index']).'?status="+$(this).val(), container: "#pjax-training-gridview", timeout: 1});
						',	
						'data-pjax' => true,
					],
				]).
				'</div>',
			'after'=>Html::a('<i class="fa fa-fw fa-repeat"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
			'showFooter'=>false
		],
		'responsive'=>true,
		'hover'=>true,
    ]); ?>

    <?php \yii\widgets\Pjax::end(); ?>

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
		
		echo Html::beginTag('div', ['class'=>'col-md-8']);
			$form = \yii\bootstrap\ActiveForm::begin([
				'options'=>['enctype'=>'multipart/form-data'],
				'action'=>['import'],
			]);
			echo \kartik\widgets\FileInput::widget([
				'name' => 'importFile', 
				'pluginOptions' => [
					'previewFileType' => 'any',
					'uploadLabel'=>"Import Excel",
				]
			]);
			\yii\bootstrap\ActiveForm::end();
		echo Html::endTag('div');
		
	echo Html::endTag('div');
	?>

</div>
