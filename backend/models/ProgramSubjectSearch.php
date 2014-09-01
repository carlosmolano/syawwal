<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ProgramSubject;

/**
 * ProgramSubjectSearch represents the model behind the search form about `backend\models\ProgramSubject`.
 */
class ProgramSubjectSearch extends ProgramSubject
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tb_program_id', 'ref_subject_type_id', 'sort', 'test', 'status', 'createdBy', 'modifiedBy', 'deletedBy'], 'integer'],
			[['hours'], 'number'],
            [['name', 'created', 'modified', 'deleted'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ProgramSubject::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tb_program_id' => $this->tb_program_id,
            'ref_subject_type_id' => $this->ref_subject_type_id,
            'hours' => $this->hours,
            'sort' => $this->sort,
            'test' => $this->test,
            'status' => $this->status,
            'created' => $this->created,
            'createdBy' => $this->createdBy,
            'modified' => $this->modified,
            'modifiedBy' => $this->modifiedBy,
            'deleted' => $this->deleted,
            'deletedBy' => $this->deletedBy,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
