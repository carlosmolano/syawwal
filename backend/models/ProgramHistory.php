<?php

namespace backend\models;

use Yii;
																			
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "tb_program_history".
 *

 * @property integer $tb_program_id
 * @property integer $revision
 * @property integer $ref_satker_id
 * @property string $number
 * @property string $name
 * @property decimal $hours
 * @property integer $days
 * @property integer $test
 * @property integer $type
 * @property string $note
 * @property integer $validationStatus
 * @property string $validationNote
 * @property string $category
 * @property string $stage 
 * @property integer $status
 * @property string $created
 * @property integer $createdBy
 * @property string $modified
 * @property integer $modifiedBy
 * @property string $deleted
 * @property integer $deletedBy
 */
class ProgramHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_program_history';
    }
	
    /**
     * @inheritdoc
     */	
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                        \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created','modified'],
                        \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'modified',
                ],
                'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                        \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['createdBy','modifiedBy'],
                        \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'modifiedBy',
                ],
            ],
        ];
    }
	

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tb_program_id', 'revision', 'name'], 'required'],
            [['tb_program_id', 'revision', 'ref_satker_id', 'days', 'test', 'type', 'validationStatus', 'status', 'createdBy', 'modifiedBy', 'deletedBy'], 'integer'],
			[['hours'], 'number'],
            [['created', 'modified', 'deleted'], 'safe'],
            [['number'], 'string', 'max' => 15],
			[['category', 'stage'], 'string', 'max' => 100],
            [['name', 'note', 'validationNote'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tb_program_id' => 'Program',
            'revision' => 'Revision',
            'ref_satker_id' => 'Satker',
            'number' => 'Number',
            'name' => 'Name',
            'hours' => 'Hours',
            'days' => 'Days',
            'test' => 'Test',
            'type' => 'Type',
            'note' => 'Note',
            'validationStatus' => 'Validation Status',
            'validationNote' => 'Validation Note',
			'category' => 'Category',
			'stage' => 'Stage',
            'status' => 'Status',
            'created' => 'Created',
            'createdBy' => 'Created By',
            'modified' => 'Modified',
            'modifiedBy' => 'Modified By',
            'deleted' => 'Deleted',
            'deletedBy' => 'Deleted By',
        ];
    }
	
	public static function getRevision($id){
		return self::find()->where(['tb_program_id' => $id,])->max('revision');
	}
}
