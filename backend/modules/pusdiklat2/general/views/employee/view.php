<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Employee */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Employees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$controller = $this->context;
$menus = $controller->module->getMenuItems();
$this->params['sideMenu'][$controller->module->uniqueId]=$menus;
?>
<div class="employee-view">

    <?= DetailView::widget([
        'model' => $model,
		'mode'=>DetailView::MODE_VIEW,
		'panel'=>[
			'heading'=>'<i class="fa fa-fw fa-globe"></i> '.'Employees # ' . $model->id,
			'type'=>DetailView::TYPE_DEFAULT,
		],
		'buttons1'=> Html::a('<i class="fa fa-fw fa-arrow-left"></i>',['index'],
						['class'=>'btn btn-xs btn-primary',
						 'title'=>'Back to Index',
						]).' '.
					 Html::a('<i class="fa fa-fw fa-trash-o"></i>',['#'],
						['class'=>'btn btn-xs btn-danger kv-btn-delete',
						 'title'=>'Delete', 'data-method'=>'post', 'data-confirm'=>'Are you sure you want to delete this item?']),
        'attributes' => [
            'id',
            [
				'attribute' => 'ref_satker_id',
				'value' => $model->satker->name,
			],
            'ref_satker_id',
            [
				'attribute' => 'ref_unit_id',
				'value' => $model->unit->name,
			],
            'ref_unit_id',
            [
				'attribute' => 'ref_religion_id',
				'value' => $model->religion->name,
			],
            'ref_religion_id',
            [
				'attribute' => 'ref_rank_class_id',
				'value' => $model->rankClass->name,
			],
            'ref_rank_class_id',
            [
				'attribute' => 'ref_graduate_id',
				'value' => $model->graduate->name,
			],
            'ref_graduate_id',
            [
				'attribute' => 'ref_sta_unit_id',
				'value' => $model->staUnit->name,
			],
            'ref_sta_unit_id',
            'name',
            'nickName',
            'frontTitle',
            'backTitle',
            'nip',
            'born',
            'birthDay',
            'gender',
            'phone',
            'email:email',
            'address',
            'married',
            'photo',
            'blood',
            'position',
            'education',
            'officePhone',
            'officeFax',
            'officeEmail:email',
            'officeAddress',
            'document1',
            'document2',
            'status',
            'created',
            'createdBy',
            'modified',
            'modifiedBy',
            'deleted',
            'deletedBy',
            'user_id',
            'public_email:email',
            'gravatar_email:email',
            'gravatar_id',
            'location',
            'bio:ntext',
            'website',
        ],
    ]) ?>

</div>
