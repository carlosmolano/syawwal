<?php

namespace backend\modules\bdk\general\controllers;

use Yii;
use backend\models\Meeting;
use backend\models\ActivityRoom;
use backend\models\MeetingSearch;
use backend\models\Room;
use backend\models\RoomSearch;
use backend\models\Satker;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Meeting3Controller implements the CRUD actions for Meeting model.
 */
class MeetingRequestController extends Controller
{
	public $layout = '@hscstudio/heart/views/layouts/column2';	 
 	
	public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
					'set' => ['post'],
					'unset' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Meeting models.
     * @return mixed
     */
    public function actionIndex($executor='all')
    {
        $searchModel = new MeetingSearch();
		$ref_satker_id = (int)Yii::$app->user->identity->employee->ref_satker_id;
		if($executor=='all'){
			$queryParams['MeetingSearch']= [
				'ref_satker_id'=>$ref_satker_id,
				'status'=>1,
			];
		}
		else{
			$queryParams['MeetingSearch']=[
				'ref_satker_id'=>$ref_satker_id,
				'status'=>1,
				'executor'=>$executor,
			];
		}
        $queryParams=yii\helpers\ArrayHelper::merge(Yii::$app->request->getQueryParams(),$queryParams);
        $dataProvider = $searchModel->search($queryParams);
		$dataProvider->getSort()->defaultOrder = ['startTime'=>SORT_DESC,'finishTime'=>SORT_DESC];
		
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'executor'=>$executor,
        ]);
    }

    /**
     * Displays a single Meeting model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Meeting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Meeting();
        if ($model->load(Yii::$app->request->post())){
			$model->ref_satker_id = (int)Yii::$app->user->identity->employee->ref_satker_id;
			$model->executor = 'GENERAL3'; // SUBBID ASSET
			if($model->save()) {
				 Yii::$app->session->setFlash('success', 'Data saved');
			}
			else{
				 Yii::$app->session->setFlash('error', 'Unable create there are some error');
			}
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Meeting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);       
		if($model->ref_satker_id!=(int)Yii::$app->user->identity->employee->ref_satker_id){
			return $this->redirect(['index']);
		}
		if($model->executor != 'GENERAL3'){
			return $this->redirect(['index']);
		}
        if ($model->load(Yii::$app->request->post())) {
            	
            if($model->save()){
				Yii::$app->session->setFlash('success', 'Data saved');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                // error in saving model
				Yii::$app->session->setFlash('error', 'There are some errors');
            }            
        }
		else{
			//return $this->render(['update', 'id' => $model->id]);
			return $this->render('update', [
                'model' => $model,
            ]);
		}
    }

    /**
     * Deletes an existing Meeting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
	 
	/* GENERAL CANNOT DELETE, ONLY EDIT 
    public function actionDelete($id)
    {
        $model=$this->findModel($id);
		if($model->ref_satker_id!=(int)Yii::$app->user->identity->employee->ref_satker_id){
			return $this->redirect(['index']);
		}
		if($model->executor != 'GENERAL3'){
			return $this->redirect(['index']);
		}
		$model->delete();
        return $this->redirect(['index']);
    }
	*/

    /**
     * Finds the Meeting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Meeting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Meeting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	public function actionEditable() {
		$model = new Meeting; // your model can be loaded here
		// Check if there is an Editable ajax request
		if (isset($_POST['hasEditable'])) {
			// read your posted model attributes
			if ($model->load($_POST)) {
				// read or convert your posted information
				$model2 = $this->findModel($_POST['editableKey']);
				$name=key($_POST['Meeting'][$_POST['editableIndex']]);
				$value=$_POST['Meeting'][$_POST['editableIndex']][$name];
				$model2->$name = $value ;
				$model2->save();
				// return JSON encoded output in the below format
				echo \yii\helpers\Json::encode(['output'=>$value, 'message'=>'']);
				// alternatively you can return a validation error
				// echo \yii\helpers\Json::encode(['output'=>'', 'message'=>'Validation error']);
			}
			// else if nothing to do always return an empty JSON encoded output
			else {
				echo \yii\helpers\Json::encode(['output'=>'', 'message'=>'']);
			}
		return;
		}
		// Else return to rendering a normal view
		return $this->render('view', ['model'=>$model]);
	}

	public function actionOpenTbs($filetype='docx'){
		$dataProvider = new ActiveDataProvider([
            'query' => Meeting::find(),
        ]);
		
		try {
			$templates=[
				'docx'=>'ms-word.docx',
				'odt'=>'open-document.odt',
				'xlsx'=>'ms-excel.xlsx'
			];
			// Initalize the TBS instance
			$OpenTBS = new \hscstudio\heart\extensions\OpenTBS; // new instance of TBS
			// Change with Your template kaka
			$template = Yii::getAlias('@hscstudio/heart').'/extensions/opentbs-template/'.$templates[$filetype];
			$OpenTBS->LoadTemplate($template); // Also merge some [onload] automatic fields (depends of the type of document).
			$OpenTBS->VarRef['modelName']= "Meeting";
			$data1[]['col0'] = 'id';			
			$data1[]['col1'] = 'ref_satker_id';			
			$data1[]['col2'] = 'name';			
			$data1[]['col3'] = 'startTime';			
	
			$OpenTBS->MergeBlock('a', $data1);			
			$data2 = [];
			foreach($dataProvider->getModels() as $meeting){
				$data2[] = [
					'col0'=>$meeting->id,
					'col1'=>$meeting->ref_satker_id,
					'col2'=>$meeting->name,
					'col3'=>$meeting->startTime,
				];
			}
			$OpenTBS->MergeBlock('b', $data2);
			// Output the result as a file on the server. You can change output file
			$OpenTBS->Show(OPENTBS_DOWNLOAD, 'result.'.$filetype); // Also merges all [onshow] automatic fields.			
			exit;
		} catch (\yii\base\ErrorException $e) {
			 Yii::$app->session->setFlash('error', 'Unable export there are some error');
		}	
		
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);		
	}	
	
	public function actionPhpExcel($filetype='xlsx',$template='yes',$engine='')
    {
		$dataProvider = new ActiveDataProvider([
            'query' => Meeting::find(),
        ]);
		
		try {
			if($template=='yes'){
				// only for filetypr : xls & xlsx
				if(in_array($filetype,['xlsx','xls'])){
					$types=['xls'=>'Excel5','xlsx'=>'Excel2007'];
					$objReader = \PHPExcel_IOFactory::createReader($types[$filetype]);
					$template = Yii::getAlias('@hscstudio/heart').'/extensions/phpexcel-template/ms-excel.'.$filetype;
					$objPHPExcel = $objReader->load($template);
					$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
					$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
					$objPHPExcel->getProperties()->setTitle("PHPExcel in Yii2Heart");
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A1', 'Tabel Meeting');
					$idx=2; // line 2
					foreach($dataProvider->getModels() as $meeting){
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$idx, $meeting->id)
													  ->setCellValue('B'.$idx, $meeting->ref_satker_id)
													  ->setCellValue('C'.$idx, $meeting->name)
													  ->setCellValue('D'.$idx, $meeting->startTime);
						$idx++;
					}		
					
					// Redirect output to a client�s web browser
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Disposition: attachment;filename="result.'.$filetype.'"');
					header('Cache-Control: max-age=0');
					$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, $types[$filetype]);
					$objWriter->save('php://output');
					exit;
				}
				else{
					Yii::$app->session->setFlash('error', 'Unfortunately pdf not support, only for excel');
				}
			}
			else{
				if(in_array($filetype,['xlsx','xls'])){
					$types=['xls'=>'Excel5','xlsx'=>'Excel2007'];
					// Create new PHPExcel object
					$objPHPExcel = new \PHPExcel();
					$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
					$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
					$objPHPExcel->getProperties()->setTitle("PHPExcel in Yii2Heart");
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A1', 'Tabel Meeting');
					$idx=2; // line 2
					foreach($dataProvider->getModels() as $meeting){
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$idx, $meeting->id)
													  ->setCellValue('B'.$idx, $meeting->ref_satker_id)
													  ->setCellValue('C'.$idx, $meeting->name)
													  ->setCellValue('D'.$idx, $meeting->startTime);
						$idx++;
					}		
									
					// Redirect output to a client�s web browser (Excel2007)
					if($filetype=='xlsx')
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					// Redirect output to a client�s web browser (Excel5)
					if($filetype=='xls')
					header('Content-Type: application/vnd.ms-excel');

					header('Content-Disposition: attachment;filename="result.'.$filetype.'"');
					header('Cache-Control: max-age=0');
					// If you're serving to IE 9, then the following may be needed
					header('Cache-Control: max-age=1');

					// If you're serving to IE over SSL, then the following may be needed
					header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
					header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
					header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
					header ('Pragma: public'); // HTTP/1.0

					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $types[$filetype]);
					$objWriter->save('php://output');
					exit;				
				}
				else if(in_array($filetype,['pdf'])){
					if(in_array($engine,['tcpdf','mpdf',''])){
						$types=['xls'=>'Excel5','xlsx'=>'Excel2007'];
						if($engine=='tcpdf' or $engine==''){
							$rendererName = \PHPExcel_Settings::PDF_RENDERER_TCPDF;
							$rendererLibraryPath = Yii::getAlias('@hscstudio/heart').'/libraries/tcpdf';
						}
						else if($engine=='mpdf'){
							$rendererName = \PHPExcel_Settings::PDF_RENDERER_MPDF;
							$rendererLibraryPath = Yii::getAlias('@hscstudio/heart').'/libraries/mpdf';
						}
						// Create new PHPExcel object
						$objPHPExcel = new \PHPExcel();
						
						$objPHPExcel->getProperties()->setTitle("PHPExcel in Yii2Heart");
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A1', 'Tabel Meeting');
						$idx=2; // line 2
						foreach($dataProvider->getModels() as $meeting){
							$objPHPExcel->getActiveSheet()->setCellValue('A'.$idx, $meeting->id)
														  ->setCellValue('B'.$idx, $meeting->ref_satker_id)
														  ->setCellValue('C'.$idx, $meeting->name)
														  ->setCellValue('D'.$idx, $meeting->startTime);
							$idx++;
						}		
						
						if (!\PHPExcel_Settings::setPdfRenderer(
							$rendererName,
							$rendererLibraryPath
						)){
							Yii::$app->session->setFlash('error', 
								'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
								'<br />' .
								'at the top of this script as appropriate for your directory structure'
							);
						}
						else{
							// Redirect output to a client�s web browser (PDF)
							header('Content-Type: application/pdf');
							header('Content-Disposition: attachment;filename="result.pdf"');
							header('Cache-Control: max-age=0');

							$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
							$objWriter->save('php://output');
							exit;
						}
					}
					else{
						Yii::$app->session->setFlash('error', 'Unfortunately this engine not support');
					}
				}
				else{
					Yii::$app->session->setFlash('error', 'Unfortunately filetype not support, only for excel & pdf');
				}
			}
        } catch (\yii\base\ErrorException $e) {
			 Yii::$app->session->setFlash('error', 'Unable export there are some error');
		}	
		
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);	
    }
	
	public function actionImport(){
		$dataProvider = new ActiveDataProvider([
            'query' => Meeting::find(),
        ]);
		
		/* 
		Please read guide of upload https://github.com/yiisoft/yii2/blob/master/docs/guide/input-file-upload.md
		maybe I do mistake :)
		*/		
		if (!empty($_FILES)) {
			$importFile = \yii\web\UploadedFile::getInstanceByName('importFile');
			if(!empty($importFile)){
				$fileTypes = ['xls','xlsx']; // File extensions allowed
				//$ext = end((explode(".", $importFile->name)));
				$ext=$importFile->extension;
				if(in_array($ext,$fileTypes)){
					$inputFileType = \PHPExcel_IOFactory::identify($importFile->tempName );
					$objReader = \PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($importFile->tempName );
					$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
					$baseRow = 2;
					$inserted=0;
					$read_status = false;
					$err=[];
					while(!empty($sheetData[$baseRow]['A'])){
						$read_status = true;
						$abjadX=array();
						//$id=  $sheetData[$baseRow]['A'];
						$ref_satker_id=  $sheetData[$baseRow]['B'];
						$name=  $sheetData[$baseRow]['C'];
						$startTime=  $sheetData[$baseRow]['D'];
						$finishTime=  $sheetData[$baseRow]['E'];
						$note=  $sheetData[$baseRow]['F'];
						$attendanceCount=  $sheetData[$baseRow]['G'];
						$classCount=  $sheetData[$baseRow]['H'];
						$hostel=  $sheetData[$baseRow]['I'];
						$location=  $sheetData[$baseRow]['J'];
						$status=  $sheetData[$baseRow]['K'];
						//$created=  $sheetData[$baseRow]['L'];
						//$createdBy=  $sheetData[$baseRow]['M'];
						//$modified=  $sheetData[$baseRow]['N'];
						//$modifiedBy=  $sheetData[$baseRow]['O'];
						//$deleted=  $sheetData[$baseRow]['P'];
						//$deletedBy=  $sheetData[$baseRow]['Q'];

						$model2=new Meeting;
						//$model2->id=  $id;
						$model2->ref_satker_id=  $ref_satker_id;
						$model2->name=  $name;
						$model2->startTime=  $startTime;
						$model2->finishTime=  $finishTime;
						$model2->note=  $note;
						$model2->attendanceCount=  $attendanceCount;
						$model2->classCount=  $classCount;
						$model2->hostel=  $hostel;
						$model2->location=  $location;
						$model2->status=  $status;
						//$model2->created=  $created;
						//$model2->createdBy=  $createdBy;
						//$model2->modified=  $modified;
						//$model2->modifiedBy=  $modifiedBy;
						//$model2->deleted=  $deleted;
						//$model2->deletedBy=  $deletedBy;

						try{
							if($model2->save()){
								$inserted++;
							}
							else{
								foreach ($model2->errors as $error){
									$err[]=($baseRow-1).'. '.implode('|',$error);
								}
							}
						}
						catch (\yii\base\ErrorException $e){
							Yii::$app->session->setFlash('error', "{$e->getMessage()}");
							//$this->refresh();
						} 
						$baseRow++;
					}	
					Yii::$app->session->setFlash('success', ($inserted).' row inserted');
					if(!empty($err)){
						Yii::$app->session->setFlash('warning', 'There are error: <br>'.implode('<br>',$err));
					}
				}
				else{
					Yii::$app->session->setFlash('error', 'Filetype allowed only xls and xlsx');
				}				
			}
			else{
				Yii::$app->session->setFlash('error', 'File import empty!');
			}
		}
		else{
			Yii::$app->session->setFlash('error', 'File import empty!');
		}
		
		return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);					
	}
	





    public function actionRoom()
    {
    	if (Yii::$app->request->get('activity_id') != null)
    	{
            $satkerModel = Satker::find()->one();

            // Ngecek ada room ga?
            if ( ! $roomModel = Room::find()->where(['ref_satker_id' => Yii::$app->user->identity->employee->ref_satker_id])->one())
            {
                Yii::$app->session->setFlash('error', '<i class="fa fa-fw fa-times-circle"></i> No room! You should create room first!');
                return $this->redirect(Url::to(['index']));
            }

            $meetingCurrent = Meeting::find()->where(['id' => Yii::$app->request->get('activity_id')])->one();

            $activityRoomDP = new ActiveDataProvider([
                'query' => ActivityRoom::find()->where(['activity_id' => Yii::$app->request->get('activity_id')]),
                'pagination' => [
                    'pageSize' => 10
                ]
            ]);

	        $satkerItem = ArrayHelper::map(Satker::find()
	        	->select(['id','name'])
	        	->asArray()
	        	->all(),
	        'id', 'name');

            $modelRoomKosong = new Room();

    		return $this->render('room', [
                    'modelRoomKosong' => $modelRoomKosong,
                    'meetingCurrent' => $meetingCurrent,
                    'activityRoomDP' => $activityRoomDP
    			]);
    	}
        else
        {
            Yii::$app->session->setFlash('error', '<i class="fa fa-fw fa-times-circle"></i>  You need to choose meeting first to enter room request page');
            return $this->redirect(Url::to(['index']));
        }
    }




    public function actionRoomSearch()
    {
        // Cuma request ajax yg bisa panggil fungsi ini
        if ( ! Yii::$app->request->isAjax) {
            Yii::$app->session->setFlash('error', '<i class="fa fa-fw fa-times-circle"></i> Forbidden');
            return $this->redirect(['index', 'activity_id' => Yii::$app->request->post('activity_id')]);
        }

        // Ngambil data
        $modelRoomKosong = Yii::$app->request->post('Room');

        // Nyari room
        $where[] = 'ref_satker_id ='.Yii::$app->user->identity->employee->ref_satker_id;
        if ($modelRoomKosong['hostel'] != null) {
            $where[] = 'hostel ='.$modelRoomKosong['hostel'];
        }
        if ($modelRoomKosong['computer'] != null) {
            $where[] = 'computer ='.$modelRoomKosong['computer'];
        }
        if ($modelRoomKosong['capacity'] != 0) {
            $where[] = 'capacity >='.$modelRoomKosong['capacity'];
        }
        $where = implode(' AND ',$where);
        $roomList = Room::find()->where($where)->all();

        // Mbikin table
        $result = '<table class="table table-bordered table-hover table-striped">';
        $result .= '<thead>
                        <tr>
                            <th>List Room Available</th>
                            <th>Capacity</th>
                            <th>Hostel</th>
                            <th>Computer</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>';
        foreach ($roomList as $k) {
            $result .= '<tr>';
            
            $result .= '<td>'.$k->name.' ';
            if ($this->isCollide($k->id, Yii::$app->request->post('startTime'), Yii::$app->request->post('finishTime'))) {
                $result .= $this->getCollideDate($k->id, Yii::$app->request->post('startTime'), Yii::$app->request->post('finishTime'));
            }
            $result .= '</td>';

            $result .= '<td style="width:80px">'.$k->capacity.'</td>';
            
            $result .= '<td style="width:80px">';
            if ($k->hostel == 1) {
                $result .= '<i class="fa fa-fw fa-check text-success"></i>';
            }
            else {
                $result .= '<i class="fa fa-fw fa-times text-danger"></i>';
            }
            $result .= '</td>';

            $result .= '<td style="width:80px">';
            if ($k->computer == 1) {
                $result .= '<i class="fa fa-fw fa-check text-success"></i>';
            }
            else {
                $result .= '<i class="fa fa-fw fa-times text-danger"></i>';
            }
            $result .= '</td>';

            $result .= '<td style="width:80px">';
            $result .= Html::beginForm(
                            Url::to(['room-save']),
                            'post', [
                                'id' => 'order-room-form',
                            ]
                        );
            $result .= Html::hiddenInput('activity_id', Yii::$app->request->post('activity_id'));
            $result .= Html::hiddenInput('tb_room_id', $k->id);
            $result .= Html::hiddenInput('startTime', date('Y-m-d H:i:s', strtotime(Yii::$app->request->post('startTime'))));
            $result .= Html::hiddenInput('finishTime', date('Y-m-d H:i:s', strtotime(Yii::$app->request->post('finishTime'))));
            if ($this->isCollide($k->id, Yii::$app->request->post('startTime'), Yii::$app->request->post('finishTime'))) {
                $result .= '<div class="label label-danger"><i class="fa fa-fw fa-times-circle"></i>
                    Used
                </div>';
            }
            else {
                $result .= Html::submitButton('<i class="fa fa-fw fa-play"></i>Request', [
                                    'class' => 'btn btn-primary btn-xs',
                                ]);
            }
            $result .= Html::endForm();
            $result .=  '</td>';

            $result .= '</tr>';
        }
        $result .= '</tbody></table>';

        echo $result;
    }





    public function actionRoomSave()
    {
        $activityRoom = new ActivityRoom;
        $activityRoom->type = 1;
        $activityRoom->activity_id = Yii::$app->request->post('activity_id');
        $activityRoom->tb_room_id = Yii::$app->request->post('tb_room_id');
        $activityRoom->startTime = date('Y-m-d H:i:s', strtotime(Yii::$app->request->post('startTime')));
        $activityRoom->finishTime = date('Y-m-d H:i:s', strtotime(Yii::$app->request->post('finishTime')));
        $activityRoom->status = 1; // Pas bikin pertama kali, statusnya langsung request/process
        $activityRoom->note = null;

        if ($activityRoom->save())
        {
            Yii::$app->session->setFlash('success', '<i class="fa fa-fw fa-check-circle"></i>A room request has been added');
            return $this->redirect(['room', 'activity_id' => $activityRoom->activity_id]);
        }
        else
        {
            Yii::$app->session->setFlash('error', '<i class="fa fa-fw fa-times-circle"></i>Failed to save room request!');
            return $this->redirect(Url::to(['room']));
        }
    }




    public function actionRoomDelete($id)
    {
        $activityId = ActivityRoom::find()->select(['activity_id'])->where(['id' => $id])->one()->activity_id;
        if (ActivityRoom::find()->where(['id' => $id])->one()->delete())
        {
            Yii::$app->session->setFlash('success', '<i class="fa fa-fw fa-check-circle"></i>A room request has been deleted!');
            return $this->redirect(['room', 'activity_id' => $activityId]);
        }
        else
        {
            Yii::$app->session->setFlash('error', '<i class="fa fa-fw fa-times-circle"></i>Failed to delete a room request!');
            return $this->redirect(Url::to(['room'], true));
        }
    }






    private function isCollide($roomId, $startTime, $finishTime) {
        // Semua argumen ga boleh null
        if ($roomId == null or $startTime == null or $finishTime == null) {
            Yii::$app->session->setFlash('error', '<i class="fa fa-fw fa-times-circle"></i> isCollide arguments is not valid');
            return $this->redirect(Url::to(['training/index']));
        }

        // Reformat argumen
        $startTime = date('d M Y H:i:s', strtotime($startTime));
        $finishTime = date('d M Y H:i:s', strtotime($finishTime));

        // Ambil activity room
        $actRoom = ActivityRoom::find()->where(['tb_room_id' => $roomId])->all(); 

        // Cek
        if ($actRoom) {
            foreach ($actRoom as $k) {
                // Klo startTime masuk range time di argumen
                if (date('d M Y H:i:s', strtotime($k->startTime)) >= $startTime and date('d M Y H:i:s', strtotime($k->startTime)) <= $finishTime) {
                    // Artinya collide, lempar dah
                    return true;
                }
                // Klo finishTime masuk range time di argumen
                if (date('d M Y H:i:s', strtotime($k->finishTime)) >= $startTime and date('d M Y H:i:s', strtotime($k->finishTime)) <= $finishTime) {
                    // Artinya collide, lempar dah
                    return true;
                }
            }
            // Ga ada 1 pun activity yang tanggalnya collide
            return false;
        }
        else {
            // activity room ga ada
            return false;
        }

    }




    private function getCollideDate($roomId, $startTime, $finishTime) {
        // Semua argumen ga boleh null
        if ($roomId == null or $startTime == null or $finishTime == null) {
            Yii::$app->session->setFlash('error', '<i class="fa fa-fw fa-times-circle"></i> isCollide arguments is not valid');
            return $this->redirect(Url::to(['training/index']));
        }

        // Reformat argumen
        $startTime = date('d M Y H:i:s', strtotime($startTime));
        $finishTime = date('d M Y H:i:s', strtotime($finishTime));

        // Ambil activity room
        $actRoom = ActivityRoom::find()->where(['tb_room_id' => $roomId])->all(); 

        // Cek
        $fOut = '';
        if ($actRoom) {
            foreach ($actRoom as $k) {
                $fOut .= '<span class="label label-default">';
                // Klo startTime masuk range time di argumen
                if (date('d M Y H:i:s', strtotime($k->startTime)) >= $startTime and date('d M Y H:i:s', strtotime($k->startTime)) <= $finishTime) {
                    $fOut .= date('D, d M Y H:i:s', strtotime($k->startTime)). ' to ';
                }
                // Klo finishTime masuk range time di argumen
                if (date('d M Y H:i:s', strtotime($k->finishTime)) >= $startTime and date('d M Y H:i:s', strtotime($k->finishTime)) <= $finishTime) {
                    // Artinya collide, lempar dah
                    $fOut .= date('D, d M Y H:i:s', strtotime($k->finishTime));
                }
                $fOut .= '</span> ';
            }
            return $fOut;
        }
        else {
            return '';
        }

    }






	
	/**
     * Creates a new Meeting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionSet($activity_id,$tb_room_id,$status=1)
    {
        $model = new ActivityRoom();
		$model->type = 1;
		$meeting = \backend\models\Meeting::findOne($activity_id);
		$model->activity_id = (int)$activity_id;
		$model->tb_room_id = (int)$tb_room_id;
		$model->startTime = $meeting->startTime;
		$model->finishTime = $meeting->finishTime;
		$model->status = $status;
		
        if($model->save()) {
			Yii::$app->session->setFlash('success', 'Data saved');
		}
		else{
			 Yii::$app->session->setFlash('error', 'Unable create there are some error');
		}
		if (Yii::$app->request->isAjax){	
			return ('Room have set');
		}
		else{
			return $this->redirect(['room', 'activity_id' => $activity_id]);
		}
    }
	
	 public function actionUnset($activity_id,$tb_room_id)
    {
        $model = ActivityRoom::find()->where(
			'activity_id=:activity_id AND tb_room_id=:tb_room_id',[':activity_id'=>$activity_id,':tb_room_id'=>$tb_room_id])->one();
		if($model->delete()) {
			Yii::$app->session->setFlash('success', 'Data saved');
		}
		else{
			 Yii::$app->session->setFlash('error', 'Unable create there are some error');
		}
		if (Yii::$app->request->isAjax){	
			return ('Room have unset');
		}
		else{
			return $this->redirect(['room', 'activity_id' => $activity_id]);
		}
    }
}
