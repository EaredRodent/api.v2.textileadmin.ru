<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 07.02.2019
 * Time: 15:44
 */

namespace app\modules\v1\classes;

use app\modules\v1\V1Mod;
use Exception;
use Yii;
use yii\base\InlineAction;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use WebSocket\Client;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;

class ActiveControllerExtended extends ActiveController
{
    protected $wssUrl = 'ws://127.0.0.1:6001';
    protected $transaction;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'optional' => ['*']
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['options'],
                    'allow' => true,
                ],
                [
                    'actions' => [$this->action->id],
                    'allow' => true,
                ]
            ]
        ];
        return $behaviors;
    }

    /**
     * @param $action InlineAction
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action)
    {
        // СНАЧАЛА ВЫЗВАТЬ РОДИТЕЛЬСКИЙ BEFORE ACTION, ИНАЧЕ АУТЕНТИФИКАЦИЯ НЕ ПРОЙДЕТ
        // И РЕЗУЛЬТАТОМ ПРОВЕРКИ ПРАВ БУДЕТ ЧУЩШЬ!!!
        if (!parent::beforeAction($action)) {
            return false;
        }

        bcscale(6);

        Yii::$container->set('yii\data\Pagination', ['pageSizeLimit' => 100000, 'pageSize' => 100000]);
        Yii::$app->response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Cookie, Authorization');
        Yii::$app->response->headers->set('Access-Control-Allow-Credentials', 'true');
        Yii::$app->response->headers->set('Access-Control-Allow-Origin', '*');
        Yii::$app->response->headers->set('Access-Control-Expose-Headers', 'Log-Dbcount, Log-Dbtime, Log-Apptime, Log-Appmemory');

        $permission = Yii::$app->getRequest()->getMethod() . ' /' . $this->action->getUniqueId();
        if (!Yii::$app->getUser()->can($permission) && $this->action->id !== 'options') {
            throw new HttpException(403, 'From ActiveControllerExtended');
        }

        return true;
    }

    public function afterAction($action, $result)
    {

        $result = parent::afterAction($action, $result);

        // your custom code here

        $logger = Yii::getLogger();

        $dbCountQuery = $logger->getDbProfiling()[0];
        $dbTime = round($logger->getDbProfiling()[1], 3);
        $appTime = round($logger->elapsedTime, 3);
        $appMemory = number_format(memory_get_peak_usage(), 0, '', ' ');

        $headers = Yii::$app->response->headers;

        $headers->add('Log-Dbcount', $dbCountQuery);
        $headers->add('Log-Dbtime', $dbTime);
        $headers->add('Log-Apptime', $appTime);
        $headers->add('Log-Appmemory', $appMemory);


        return $result;
    }

    public function runAction($id, $params = [])
    {
        $this->transaction = Yii::$app->db->beginTransaction();
        try {
            $params = $this->mergeWithPostParams($params);

            $result = parent::runAction($id, $params);

            $this->transaction->commit();

            /** @var V1Mod $module */
//            $module = Yii::$app->getModule('v1');
//            $errors = $module->cmdErrors;
//            if (empty($errors)) {
//                $this->transaction->commit();
//            } else {
//                throw new \Exception("{$module->cmdErrors[0]}");
//            }

            // Отправка измененных таблиц по WebSocket
            /** @var V1Mod $module */
            $module = Yii::$app->getModule('v1');
            if (!empty($module->cmdTables)) {
                $wsc = new Client($this->wssUrl);
                $wsc->send(json_encode($module->cmdTables));
            }
            return $result;
        } catch (Exception $e) {
            $this->transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @param $getParams array
     * @return array
     * @throws InvalidConfigException
     */
    private function mergeWithPostParams($getParams)
    {
        return array_merge($getParams,
            Yii::$app->request->getBodyParams());
    }
}
