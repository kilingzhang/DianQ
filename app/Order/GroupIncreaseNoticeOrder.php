<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/8/26
 * Time: 0:46
 */

namespace App\Order;


use App\Core\BaseOrder;
use App\Support\Msg;
use CoolQSDK\CoolQ;
use CoolQSDK\CQ;
use CoolQSDK\Response;

class GroupIncreaseNoticeOrder extends BaseOrder
{

    public function getOrderName()
    {
        return 'GroupIncreaseNoticeOrder';
    }


    public function run(CoolQ $coolQ, array $content)
    {

        $event = $content['event'];//兼容4.0
        switch ($event) {
            //群管理员变动
            case "group_admin":

                $this->content = [
                    'event' => empty($content['event']) ? $content['notice_type'] : $content['event'],
                    //"set"、"unset"	事件子类型，分别表示设置和取消管理员
                    'sub_type' => $content['sub_type'],
                    'group_id' => $content['group_id'],
                    'user_id' => $content['user_id'],
                ];

                break;
            //群成员减少
            case "group_decrease":

                $this->content = [
                    'event' => empty($content['event']) ? $content['notice_type'] : $content['event'],
                    //"leave"、"kick"、"kick_me"	事件子类型，分别表示主动退群、成员被踢、登录号被踢
                    'sub_type' => $content['sub_type'],
                    'group_id' => $content['group_id'],
                    'user_id' => $content['user_id'],
                    'operator_id' => $content['operator_id'],
                ];

                break;
            //群成员增加
            case "group_increase":

                $this->content = [
                    'event' => empty($content['event']) ? $content['notice_type'] : $content['event'],
                    //"approve"、"invite"	事件子类型，分别表示管理员已同意入群、管理员邀请入群
                    'sub_type' => $content['sub_type'],
                    'group_id' => $content['group_id'],
                    'user_id' => $content['user_id'],
                    'operator_id' => $content['operator_id'],
                ];

                $coolQ->sendGroupMsgAsync($content['group_id'], CQ::at($content['user_id']) . "\n" . $this->getGongGongGroupIncreaseNotice(), false);

                if ($coolQ->isWhiteList() && !in_array($content['group_id'], $coolQ->getGroupWhiteList())) {
                    return Response::banAccountError();
                }

                if (!$coolQ->isWhiteList() && $coolQ->isBlackList() && in_array($content['group_id'], $coolQ->getGroupBlackList())) {
                    return Response::banAccountError();
                }

                $coolQ->sendPrivateMsgAsync($content['user_id'], $this->getGongGongGroupIncreaseNotice(), false);


                break;
            //群文件上传
            case "group_upload":


                $this->content = [
                    'event' => empty($content['event']) ? $content['notice_type'] : $content['event'],
                    'group_id' => $content['group_id'],
                    'user_id' => $content['user_id'],
                    #字段名	数据类型	说明
                    #id	string	文件 ID
                    #name	string	文件名
                    #size	number	文件大小（字节数）
                    #busid	number	busid（目前不清楚有什么作用）
                    'file' => $content['file'],
                ];

                break;
            //好友添加
            case "friend_added":

                $this->content = [
                    'event' => empty($content['event']) ? $content['notice_type'] : $content['event'],
                    'user_id' => $content['user_id'],
                ];

                break;
        }


    }

    private function getGongGongGroupIncreaseNotice()
    {
        $notic = env('GONGGONG_GROUP_INCREASE_NOTICE');
        $notic = str_replace('<br>', "\n", $notic);
        return $notic;
    }

}