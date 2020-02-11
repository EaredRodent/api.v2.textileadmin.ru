<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 1/24/2020
 * Time: 10:45 AM
 */

namespace app\modules\v1\controllers;


use app\modules\AppMod;
use app\modules\v1\classes\ActiveControllerExtended;
use yii\httpclient\Client;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;

class ServiceController extends ActiveControllerExtended
{
    public $modelClass = '';

    const actionReloadAllContacts = 'POST /v1/service/reload-all-contacts';

    /**
     * Рассылает сигнал о перезагрузке страницы всем контактным лицам
     * @param $secret_key
     */
    function actionReloadAllContacts($secret_key)
    {
        $wsc = new \WebSocket\Client(AppMod::wssUrl);
        try {
            $wsc->send(json_encode([
                'type' => 'RELOAD',
                'token' => $secret_key
            ]));
        } catch (\Exception $ee) {

        }
    }

    const actionDeploy = 'POST /v1/service/deploy';

    /**
     * Генерирует мета-файл с информацией о всех проектах
     * @param $secret_key [IuiKnzwda6xFtjeTd92K]
     * @return array
     * @throws ForbiddenHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    function actionDeploy($secret_key)
    {
        if ($secret_key !== AppMod::metaGenerateSecretKey) {
            throw new ForbiddenHttpException();
        }

        $client = new Client();

        $metaFile = [
            'textile-spa' => [],
            'textile-v3' => [],
            'b2b-oxouno' => [],
            'textile-api' => [],
            'date' => ''
        ];

        // Заполнение информации о проектах

        foreach ($metaFile as $projectName => &$projectInfo) {
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl('https://api.github.com/repos/ralex123/' . $projectName . '/branches/master')
                ->setHeaders([
                    'User-Agent' => 'x3RABBITx3',
                    'Authorization' => AppMod::gitHubAuthorizationHeader
                ])
                ->send();

            if ($response->isOk) {
                $resp = $response->getData();

                $projectInfo['hash'] = $resp['commit']['sha'];
                $projectInfo['date'] = date('d.m.Y H:i:s', strtotime($resp['commit']['commit']['committer']['date']));
                $projectInfo['message'] = $resp['commit']['commit']['message'];

            } else {
                $projectInfo = [];
            }
        }

        // TS создания файла

        $metaFile['date'] = date('d.m.Y H:i:s');

        $metaFileStr = json_encode($metaFile, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);

        file_put_contents(\Yii::getAlias(AppMod::fileToGitJson), $metaFileStr);

        return $metaFile;
    }

    const actionGetDeployJson = 'GET /v1/service/get-deploy-json';

    /**
     * Выдает сгенерированный мета-файл с хешами актуальных коммитов
     * @return mixed
     */
    public function actionGetDeployJson()
    {
        $file = file_get_contents(\Yii::getAlias(AppMod::fileToGitJson));
        return json_decode($file, true);
    }

    /**
     * Токен, который используется клиентом для мониторинга WSS (TA)
     */
    const actionGetWssMonitoringInfo = 'GET /v1/service/get-wss-monitoring-info';

    public function actionGetWssMonitoringInfo()
    {
        $userList = [];

        $wsc = new \WebSocket\Client(AppMod::wssUrl);
        try {
            $wsc->send(json_encode([
                'type' => 'MONITORING',
                'token' => AppMod::wsSenderSecretKey
            ]));

            $message = $wsc->receive();
            $message = json_decode($message, true);
            if (isset($message['data'])) {
                $userList = $message['data'];
            }
        } catch (Exception $ee) {
        }

        return $userList;
    }
}