<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 14.01.2019
 * Time: 15:53
 */

namespace app\modules\v1\controllers;

use app\models\AnxUser;
use app\modules\AppMod;
use app\modules\v1\V1Mod;
use app\services\ServReCAPTCHA;
use app\modules\v1\classes\ActiveControllerExtended;
use app\modules\v1\models\sls\SlsClient;
use Yii;
use yii\web\HttpException;


class SlsClientController extends ActiveControllerExtended
{
    /** @var SlsClient $modelClass */
    public $modelClass = 'app\modules\v1\models\sls\SlsClient';

    const actionGetForFilters = 'GET /v1/sls-client/get-for-filters';

    /**
     * Вернуть список клиентов ссортировкой по short_name
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionGetForFilters()
    {
        return SlsClient::find()
            ->orderBy('short_name')
            ->all();
    }

    const actionGetLegalEntitiesByOrgId = 'GET /v1/sls-client/get-legal-entities-by-org-id';

    /**
     * Возвращает юр.лиц для организации (b2b)
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    function actionGetLegalEntitiesByOrgId($id)
    {
        return SlsClient::find()->where(['org_fk' => $id])->all();
    }


    const actionGetOutdatedLegalEntities = 'GET /v1/sls-client/get-outdated-legal-entities';

    /**
     * Проучает список юр.лиц с старого ТА
     * @return SlsClient[]
     */
    public function actionGetOutdatedLegalEntities()
    {
        return SlsClient::find()
            ->where(['org_fk' => null])
            ->orderBy('short_name')
            ->all();
    }

    const actionImportLegalEntity = 'POST /v1/sls-client/import-legal-entity';

    /**
     * Импортирует юр.лицо из старого TA в организацию $org_fk
     * @param $id
     * @param $org_fk
     * @return array
     * @throws HttpException
     */
    public function actionImportLegalEntity($id, $org_fk)
    {
        $slsClient = SlsClient::get($id);
        $slsClient->org_fk = $org_fk;

        if (!$slsClient->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        return ['_result_' => 'success'];
    }

    const actionCreateUpdateForOrg = 'POST /v1/sls-client/create-update-for-org';

    /**
     * Создает или редактирует юр.лицо
     * @param $form
     * @return array
     * @throws HttpException
     */
    public function actionCreateUpdateForOrg($form)
    {
        $form = json_decode($form, true);

        // Проверка, чтобы не указали 2 раза один и тот де ИНН (Дрибан - Лариса поменяла ИНН)
        $id = (isset($form["id"])) ? $form["id"] : null;
        $inn = (isset($form["inn"])) ? $form["inn"] : '';
        if ($inn) {
            $existInn = SlsClient::find()
                ->where(['inn' => $inn])
                ->andFilterWhere(['!=', 'id', $id])
                ->count();
            if ($existInn > 0) {
                throw new HttpException(200,
                    'Такой ИНН уже существует. Если ИНН отсутствует - оставляйте пустую строку', 200);
            }
        }

        if (isset($form['id'])) {
            $slsClient = SlsClient::get($form['id']);
            $slsClient->attributes = $form;
        } else {
            $slsClient = new SlsClient();
            $slsClient->attributes = $form;
        }

        if (!$slsClient->save()) {
            throw new HttpException(200, 'Внутренняя ошибка.', 200);
        }

        return ['_result_' => 'success'];
    }

    const actionGetLegalEntities = 'GET /v1/sls-client/get-legal-entities';

    /**
     * Возвращает юр.лиц для организации текущего контакта (B2B)
     * @param string $userId [currentUser|57]
     * @return array|\yii\db\ActiveRecord[]
     * @throws HttpException
     * @throws \Throwable
     */
    function actionGetLegalEntities($userId = 'currentUser')
    {

        if (!$userId) {
            $userId = 'currentUser';
        }

        /** @var AnxUser $contact */
        if ($userId === 'currentUser') {
            $contact = Yii::$app->getUser()->getIdentity();
        } else {
            if (YII_ENV_PROD) {
                throw new HttpException(200, "Не надо шалить", 200);
            }
            $contact = AnxUser::findOne((int)$userId);
        }

        $orgId = $contact->org_fk;

        if ($orgId > 0) {
            return SlsClient::find()
                ->where(['org_fk' => $orgId])
                ->all();
        } else {
            throw new HttpException(200, "Не найдены юридические лица для \$userId = $userId", 200);
        }
    }

    const actionUploadDocsFromContact = 'POST /v1/sls-client/upload-docs-from-contact';

    /**
     * Загрузка документов контактным лицом для юр.лица $legalEntityID и типом документа $docType
     * @param $legalEntityID
     * @param $docType
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionUploadDocsFromContact($legalEntityID, $docType)
    {
        $slsClient = SlsClient::findOne($legalEntityID);

        if (!$slsClient) {
            throw new HttpException(200, "Юр. лицо с этим ID не существует.", 200);
        }

        /** @var AnxUser $contact */
        $contact = Yii::$app->getUser()->getIdentity();

        if ($contact->org_fk !== $slsClient->org_fk) {
            throw new HttpException(200, "Юр. лицо с этим ID не состоит в вашей организации.", 200);
        }

        return $this->uploadDocs($slsClient, $docType);
    }

    const actionUploadDocsFromManager = 'POST /v1/sls-client/upload-docs-from-manager';

    /**
     * Загрузка документов менеджером для юр.лица $legalEntityID и типом документа $docType
     * @param $legalEntityID
     * @param $docType
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionUploadDocsFromManager($legalEntityID, $docType)
    {
        $slsClient = SlsClient::findOne($legalEntityID);

        if (!$slsClient) {
            throw new HttpException(200, "Юр. лицо с этим ID не существует.", 200);
        }

        return $this->uploadDocs($slsClient, $docType);
    }

    /**
     * Загружает документы для юр.лица $slsClient и типом документа $docType
     * @param SlsClient $slsClient
     * @param $docType
     * @return array
     * @throws HttpException
     */
    private function uploadDocs($slsClient, $docType)
    {
        $files = [];
        if (!isset($_FILES['files'])) {
            throw new HttpException(200, "Файлы отсутствуют.", 200);
        }

        $files = $_FILES['files'];

        $destDir = Yii::getAlias(AppMod::filesRout[$docType]);

        $rmFiles = glob($destDir . '/' . $slsClient->id . '_*');

        foreach ($rmFiles as $rmFile) {
            unlink($rmFile);
        }

        if (!$destDir) {
            throw new HttpException(200, "Этот тип документа не обрабатывается.", 200);
        }

        $filesCount = isset($files['name']) ? count($files['name']) : 0;
        for ($i = 0; $i < $filesCount; $i++) {
            $pathInfo = pathinfo($files['name'][$i]);

            $fileName = $slsClient->id . '_' . $i . '.' . $pathInfo['extension'];
            $dest = $destDir . '/' . $fileName;

            move_uploaded_file($files['tmp_name'][$i], $dest);
        }

        // Модель не обновлялась, но нужно уведомить о загруженном файле для юр. лица

        Yii::$app->getModule('v1')->cmdTables[] = 'sls_client';

        //

        return ['_result_' => 'success'];
    }

    const actionGetDocsForContact = 'GET /v1/sls-client/get-docs-for-contact';

    /**
     * Возвращает документы для всех юр.лиц, состоящих в той же организации, что и контактное лицо $contactLogin
     * @param string $contactLogin
     * @return array
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionGetDocsForContact($contactLogin = 'CurrentUser')
    {
        if (YII_ENV_PROD && ($contactLogin !== 'CurrentUser')) {
            throw new HttpException(200, "Только текущий пользователь.", 200);
        }

        if ($contactLogin === 'CurrentUser') {
            /** @var AnxUser $contact */
            $contact = Yii::$app->getUser()->getIdentity();
        } else {
            $contact = AnxUser::findOne(['login' => $contactLogin]);
        }

        if (!$contact) {
            throw new HttpException(200, "Пользователь не получен.", 200);
        }

        if (!$contact->org_fk) {
            throw new HttpException(200, "Пользователь не состоит в организации.", 200);
        }

        $slsClients = SlsClient::findAll(['org_fk' => $contact->org_fk]);

        return $this->getDocs($slsClients);
    }

    const actionGetDocsForManager = 'GET /v1/sls-client/get-docs-for-manager';

    /**
     * Возвращает документы для всех юр.лиц, состоящих в организации $orgID
     * @param $orgID
     * @return array
     * @throws HttpException
     */
    public function actionGetDocsForManager($orgID)
    {
        if (!$orgID) {
            throw new HttpException(200, "Укажите организацию.", 200);
        }

        $slsClients = SlsClient::findAll(['org_fk' => $orgID]);

        return $this->getDocs($slsClients);
    }


    /**
     * Возвращает документы для юр.лиц $slsClients
     * @param SlsClient[] $slsClients
     * @return array
     */
    private function getDocs($slsClients)
    {
        $response = [];

        foreach ($slsClients as $slsClient) {
            $docTypes = strpos($slsClient->short_name, 'ООО') === false ? AppMod::filesB2B_DocTypes_IP : AppMod::filesB2B_DocTypes_OOO;


            foreach ($docTypes as $docTypeDir => $docTypeName) {
                $files = glob(Yii::getAlias(AppMod::filesRout[$docTypeDir]) . '/' . $slsClient->id . '_*');

                $docTypeObj = [
                    'dir' => $docTypeDir,
                    'name' => $docTypeName,
                    'files' => []
                ];

                foreach ($files as $file) {
                    $filename = pathinfo($file)['filename'];
                    $basename = pathinfo($file)['basename'];
                    $docTypeObj['files'][] = [
                        'name' => $filename,
                        'url' => CURRENT_API_URL . '/v1/files/get/' . AppMod::apiFilesGetPublicKey . '/' . $docTypeDir . '/' . $basename
                    ];
                }

                $response[$slsClient->id][] = $docTypeObj;
            }
        }
        return $response;
    }
}
