<?php

namespace backend\models;

use Yii;
																																										
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "tb_trainer".
 *

 * @property integer $id
 * @property string $idn
 * @property integer $ref_graduate_id
 * @property integer $ref_rank_class_id
 * @property integer $ref_religion_id
 * @property string $name
 * @property string $nickName
 * @property string $frontTitle
 * @property string $backTitle
 * @property string $nip
 * @property string $born
 * @property string $birthDay
 * @property integer $gender
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property integer $married
 * @property string $photo
 * @property string $blood
 * @property integer $position
 * @property string $positionDesc
 * @property string $eselon
 * @property string $organization
 * @property integer $widyaiswara
 * @property string $education
 * @property string $educationHistory
 * @property string $trainingHistory
 * @property string $experience
 * @property string $competency
 * @property string $npwp
 * @property string $bankAccount
 * @property string $officePhone
 * @property string $officeFax
 * @property string $officeEmail
 * @property string $officeAddress
 * @property string $document1
 * @property string $document2
 * @property integer $status
 * @property string $created
 * @property integer $createdBy
 * @property string $modified
 * @property integer $modifiedBy
 * @property string $deleted
 * @property integer $deletedBy
 *
 * @property Graduate $refGraduate
 * @property RankClass $refRankClass
 * @property Religion $refReligion
 * @property TrainingClassSubject[] $trainingClassSubjects
 * @property TrainingSubjectTrainerRecommendation[] $trainingSubjectTrainerRecommendations
 * @property TrainingTrainerAttendance[] $trainingTrainerAttendances
 */
class Trainer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tb_trainer';
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
            [['idn', 'name'], 'required'],
            [['ref_graduate_id', 'position', 'ref_rank_class_id', 'ref_religion_id', 'eselon', 'gender', 'married', 'widyaiswara', 'status', 'createdBy', 'modifiedBy', 'deletedBy'], 'integer'],
            [['birthDay', 'created', 'modified', 'deleted'], 'safe'],
            [['idn', 'address', 'photo', 'positionDesc', 'education', 'competency', 'bankAccount', 'officeAddress', 'document1', 'document2'], 'string', 'max' => 255],
            [['name', 'nickName', 'born', 'phone', 'npwp', 'officePhone', 'officeFax'], 'string', 'max' => 50],
            [['frontTitle', 'backTitle'], 'string', 'max' => 20],
            [['nip'], 'string', 'max' => 18],
            [['email', 'officeEmail'], 'string', 'max' => 100],
            [['blood'], 'string', 'max' => 10],
            [['organization'], 'string', 'max' => 45],
            [['educationHistory', 'trainingHistory', 'experience'], 'string', 'max' => 1000],
            [['idn'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idn' => 'Idn',
            'ref_graduate_id' => 'Ref Graduate ID',
            'ref_rank_class_id' => 'Ref Rank Class ID',
            'ref_religion_id' => 'Ref Religion ID',
            'name' => 'Name',
            'nickName' => 'Nick Name',
            'frontTitle' => 'Front Title',
            'backTitle' => 'Back Title',
            'nip' => 'Nip',
            'born' => 'Born',
            'birthDay' => 'Birth Day',
            'gender' => 'Gender',
            'phone' => 'Phone',
            'email' => 'Email',
            'address' => 'Address',
            'married' => 'Married',
            'photo' => 'Photo',
            'blood' => 'Blood',
            'position' => 'Position',
			'positionDesc' => 'Position Description',
			'eselon' => 'Eselon',
            'organization' => 'Organization',
            'widyaiswara' => 'Widyaiswara',
            'education' => 'Education',
            'educationHistory' => 'Education History',
            'trainingHistory' => 'Training History',
            'experience' => 'Experience',
            'competency' => 'Competency',
            'npwp' => 'Npwp',
            'bankAccount' => 'Bank Account',
            'officePhone' => 'Office Phone',
            'officeFax' => 'Office Fax',
            'officeEmail' => 'Office Email',
            'officeAddress' => 'Office Address',
            'document1' => 'Document1',
            'document2' => 'Document2',
            'status' => 'Status',
            'created' => 'Created',
            'createdBy' => 'Created By',
            'modified' => 'Modified',
            'modifiedBy' => 'Modified By',
            'deleted' => 'Deleted',
            'deletedBy' => 'Deleted By',
        ];
    }
	    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGraduate()
    {
        return $this->hasOne(Graduate::className(), ['id' => 'ref_graduate_id']);
    }
	    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRankClass()
    {
        return $this->hasOne(RankClass::className(), ['id' => 'ref_rank_class_id']);
    }
	    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReligion()
    {
        return $this->hasOne(Religion::className(), ['id' => 'ref_religion_id']);
    }
	
	    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSatker()
    {
        return $this->hasOne(Satker::className(), ['id' => 'ref_satker_id']);
    }
	    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingClassSubjects()
    {
        return $this->hasMany(TrainingClassSubject::className(), ['tb_program_subject_id' => 'id']);
    }
	    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingSubjectTrainerRecommendations()
    {
        return $this->hasMany(TrainingSubjectTrainerRecommendation::className(), ['tb_trainer_id' => 'id']);
    }
	    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingTrainerAttendances()
    {
        return $this->hasMany(TrainingTrainerAttendance::className(), ['tb_trainer_id' => 'id']);
    }
	
	public function getEmployee()
    {
        return $this->hasOne(Employee::className(), ['nip' => 'nip'])->onCondition('LENGTH('.Employee::tableName().'.nip)>=9');
    }
	
	public function getStudent()
    {
        return $this->hasOne(Student::className(), ['nip' => 'nip'])->onCondition('LENGTH('.Student::tableName().'.nip)>=9');
    }
}
