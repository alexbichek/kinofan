<?php

namespace frontend\controllers;

use Yii;
use common\models\View;
use common\models\ViewSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ViewController implements the CRUD actions for View model.
 */
class ViewController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all View models.
     * @return mixed
     */

    public function actionIndex(){
        return $this->render('../view/index', [
            'views' => $this->findFilms(Yii::$app->user->id),
        ]);
    }

    protected function findFilms($id)
    {
        if (($model = View::find()->where(['userId' => $id])->all()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('You do not have any viewed film.');
        }
    }

    public function actionAdd(){
        $id = Yii::$app->user->id;
        $get = Yii::$app->request->get();
        if ($model = View::findOne(['userId' => $id, 'filmId' => $get]) !== null) {
            throw new NotFoundHttpException('You watched this film already.');
        } else {
            $view = new View();
            $view->userId = $id;
            $view->filmId = $get['id'];
            $view->estimation = 0;
            $view->save();
            return $this->goHome();
        }
    }

    public function actionDel(){
        $get = Yii::$app->request->get('id');
        $this->findFilm($get)->delete();
        return $this->actionIndex();
    }

    public function actionComplete(){
        $get = Yii::$app->request->get('id');
        $model = $this->findFilm($get);
        $model->status = 1;
        $model->save();
        return $this->actionIndex();
    }

    protected function findFilm($id)
    {
        if (($model = View::findOne(['filmId' => $id, 'userId' => Yii::$app->user->id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Some doing wrong (.');
        }
    }

    /**
     * Displays a single View model.
     * @param integer $userId
     * @param integer $filmId
     * @return mixed
     */
    public function actionView($userId, $filmId)
    {
        return $this->render('view', [
            'model' => $this->findModel($userId, $filmId),
        ]);
    }

    /**
     * Creates a new View model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new View();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'userId' => $model->userId, 'filmId' => $model->filmId]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing View model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $userId
     * @param integer $filmId
     * @return mixed
     */
    public function actionUpdate($userId, $filmId)
    {
        $model = $this->findModel($userId, $filmId);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'userId' => $model->userId, 'filmId' => $model->filmId]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing View model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $userId
     * @param integer $filmId
     * @return mixed
     */
    public function actionDelete($userId, $filmId)
    {
        $this->findModel($userId, $filmId)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the View model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $userId
     * @param integer $filmId
     * @return View the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($userId, $filmId)
    {
        if (($model = View::findOne(['userId' => $userId, 'filmId' => $filmId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
