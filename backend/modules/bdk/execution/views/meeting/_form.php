<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use kartik\datecontrol\DateControl;
use backend\models\Satker;

?>

<div class="meeting-form">
	<div class="panel panel-default">
		
		<div class="panel-heading">
			<div class="pull-right">
			<?= Html::a('<i class="fa fa-arrow-left"></i> Back',['index'],
							['class'=>'btn btn-xs btn-primary',
							 'title'=>'Back to Index',
							 'data-pjax' => '0'
							]) ?>
			</div>
			<i class="fa fa-fw fa-globe"></i> 
			Meeting	
		</div>
		
		<div class="panel-body">
		    
		    <?php $form = ActiveForm::begin([
				'options'=>[
					'type' => ActiveForm::TYPE_VERTICAL,
				]
			]); ?>
			<?= $form->errorSummary($model) ?>
			
			<div class="row">
				
				<div class="col-md-6">
				    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
					<?= $form->field($model, 'note')->textInput(['maxlength' => 255]) ?>
				    <?= $form->field($model, 'startTime')->widget(DateControl::classname(), [
									'type' => DateControl::FORMAT_DATETIME,
								]); ?>
				    <?= $form->field($model, 'finishTime')->widget(DateControl::classname(), [
									'type' => DateControl::FORMAT_DATETIME,
								]); ?>
				</div>
				
				<div class="col-md-6">
				    <?= $form->field($model, 'attendanceCount')->textInput() ?>
					
					<?php 
					if ($model->isNewRecord) {
						echo $form->field($model, 'classCount')->textInput(['maxlength' => 3, 'value'=>'1']);
					}
					else{
						echo $form->field($model, 'classCount')->textInput(['maxlength' => 3]);
					}
					?>

				    <?= $form->field($model, 'hostel')->widget(SwitchInput::classname(), [
									'pluginOptions' => [
										'onText' => 'On',
										'offText' => 'Off',
									]
								]) ?>
				    <?= $form->field($model, 'status')->widget(SwitchInput::classname(), [
									'pluginOptions' => [
										'onText' => 'On',
										'offText' => 'Off',
									]
								]) ?>

				    <?php
					
					$data = ArrayHelper::map(Satker::find()
						->select(['id','name'])
						->where('status=1')
						->asArray()
						->all(), 'id', 'name');
					echo $form->field($model, 'location')->widget(Select2::classname(), [
						'data' => array_merge($data,['-1'=>'Other']),
						'options' => ['placeholder' => 'Choose satker ...'],
						'pluginOptions' => [
							'allowClear' => true
						],
					]); 
					if($model->isNewRecord){
						$ref_satker_id = (int)Yii::$app->user->identity->employee->ref_satker_id;
						$initScript = '
						$("#meeting-location").select2().select2("val", '.$ref_satker_id.');
						$("label").attr("style","display:block;");
						//$("#room-owner").bootstrapSwitch("state", true, true);		
						';
						$this->registerJS($initScript);
					}	
					?>
				</div>

			</div>

			<div class="row">
				<div class="col-md-12">
			        <?= Html::submitButton(
						$model->isNewRecord ? '<span class="fa fa-fw fa-save"></span> '.'Create' : '<span class="fa fa-fw fa-save"></span> '.'Update', 
						['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
				</div>
			</div>
			
		    <?php ActiveForm::end(); ?>

		</div>
	</div>
</div>
