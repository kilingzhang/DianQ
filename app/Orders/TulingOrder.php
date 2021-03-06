<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/26
 * Time: 1:22
 */

namespace App\Orders;


use App\Core\BaseOrder;
use App\Support\Log;
use App\Support\Time;
use CoolQSDK\CoolQ;
use CoolQSDK\CQ;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class TulingOrder extends BaseOrder
{
    private $apiKey = '';

    private $client = null;

    private static $selfQq = 2093208406;

    public function __construct()
    {
        $this->apiKey = getenv('TULING_APIKEY');
        $this->client = new Client();
    }

    /**
     * @return int
     */
    public static function getSelfQq(): int
    {
        return self::$selfQq;
    }

    /**
     * @param int $selfQq
     */
    public static function setSelfQq(int $selfQq)
    {
        self::$selfQq = $selfQq;
    }


    public function getOrderName()
    {
        return 'TulingOrder';
    }

    public function run(CoolQ $coolQ, array $content)
    {
        switch ($content['message_type']) {
            //私聊消息
            case "private":

                if (empty($this->apiKey)) {
                    $coolQ->sendPrivateMsg($content['user_id'], 'No ApiKey');
                    return;
                }

                $data = $this->privateTuling($content['user_id'], $content['message']);
                $data = json_decode($data, true);
                if (empty($data['results'][0]['values']['text'])) {
                    //TODO: 返回空值做处理
                    return;
                }

                $coolQ->sendPrivateMsg($content['user_id'], $data['results'][0]['values']['text']);

                break;
            //群消息
            case "group":

                if (self::getSelfQq() == null) {
                    $loginInfo = json_decode($coolQ->getLoginInfo(), true);
                    if ($loginInfo['retcode'] !== 0) {
                        $coolQ->sendGroupMsg($content['group_id'], '机器人信息获取失败');
                        return;
                    }
                    self::setSelfQq($loginInfo['data']['user_id']);
                }

                if (empty($this->apiKey)) {
                    $coolQ->sendGroupMsg($content['group_id'], 'No ApiKey');
                    return;
                }

                $message = $content['message'];

                if (CQ::isAtMe($message, self::$selfQq) || $content['group_id'] == '647895869') {
                    $data = $this->groupTuling($content['user_id'], $content['group_id'], $content['group_id'], $content['message']);
                    $data = json_decode($data, true);

                    if (empty($data['results'][0]['values']['text'])) {
                        //TODO: 返回空值做处理
                        return;
                    }
                    if($content['user_id'] == 1353693508){
                        $coolQ->sendGroupMsg($content['group_id'], CQ::At($content['user_id']) . "\n" . $data['results'][0]['values']['text']);
                    }

                }


                // {"reply":"message","block": true,"at_sender":true,"kick":false,"ban":false}


                break;
            //讨论组消息
            case "discuss":


                if (empty($this->apiKey)) {
                    $coolQ->sendDiscussMsg($content['discuss_id'], 'No ApiKey');
                    return;
                }

                $data = $this->groupTuling($content['user_id'], $content['discuss_id'], $content['discuss_id'], $content['message']);
                $data = json_decode($data, true);

                if (empty($data['results'][0]['values']['text'])) {
                    //TODO: 返回空值做处理
                    return;
                }

                if($content['user_id']){
                    $coolQ->sendDiscussMsg($content['discuss_id'], $data['results'][0]['values']['text']);
                }
                // {"reply":"message","block": true,"at_sender":true}
                break;

        }

    }

    public function privateTuling($userId, $inputText, $inputImage = null, $inputMedia = null, $selfInfo = null)
    {
        $url = 'http://openapi.tuling123.com/openapi/api/v2';

        $params = [
            'reqType' => 0,
            'userInfo' => [
                'apiKey' => $this->apiKey,
                'userId' => $userId,
            ],
            'perception' => [
                'inputText' => [
                    'text' => $inputText
                ],
                'inputImage' => [
                    'url' => $inputImage,
                ],
                'inputMedia' => [
                    'url' => $inputMedia,
                ],
                'selfInfo' => [
                    'location' => $selfInfo
                ],
            ],
        ];

        $starttime = Time::getMicrotime();

        try {

            $response = $this->client->request('POST', $url, [
                RequestOptions::JSON => $params,
            ]);

            $times = Time::ComMicritime($starttime, Time::getMicrotime());
            Log::debug('/privateTuling 请求总耗时：' . $times . '秒', [$times, $response]);


            if ($response->getStatusCode() == 200) {
                return $response->getBody();
            }

        } catch (GuzzleException $e) {
        }


    }

    public function groupTuling($userId, $groupId, $userIdName, $inputText, $inputImage = null, $inputMedia = null, $selfInfo = null)
    {
        $url = 'http://openapi.tuling123.com/openapi/api/v2';
        $params = [
            'reqType' => 0,
            'userInfo' => [
                'apiKey' => $this->apiKey,
                'userId' => $userId,
                'groupId' => $groupId,
                'userIdName' => $userIdName,
            ],
            'perception' => [
                'inputText' => [
                    'text' => $inputText
                ],
                'inputImage' => [
                    'url' => $inputImage,
                ],
                'inputMedia' => [
                    'url' => $inputMedia,
                ],
                'selfInfo' => [
                    'location' => $selfInfo
                ],
            ],
        ];

        $starttime = Time::getMicrotime();

        try {
            $response = $this->client->request('POST', $url, [
                RequestOptions::JSON => $params,
            ]);

            $times = Time::ComMicritime($starttime, Time::getMicrotime());
            Log::debug('/privateTuling 请求总耗时：' . $times . '秒', [$times]);

            if ($response->getStatusCode() == 200) {
                return $response->getBody();
            }


        } catch (GuzzleException $e) {
        }

    }

}