<?php
/**
 * Created by PhpStorm.
 * User: kilingzhang
 * Date: 2018/6/17
 * Time: 2:17
 */

namespace App\Core;


use App\Support\Log;
use App\Support\Time;
use CoolQSDK\CoolQBase;
use CoolQSDK\Response;
use CoolQSDK\Url;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class CoolQ extends CoolQBase implements PluginSubject
{

    private static $plugins = array();
    private $postType;
    public $block = false;

    public function curl($uri = Url::get_version_info, $param = [], $method = 'GET')
    {
        $starttime = Time::getMicrotime();
        try {

            $response = self::$client->request($method, $uri, array_merge(self::$options, [
                'query' => $param
            ]));

            $times = Time::ComMicritime($starttime, Time::getMicrotime());
            Log::debug($uri . ' 请求总耗时：' . $times . '秒', [
                'request' => $param,
                'status' => $response->getStatusCode(),
                'response' => $response->getBody(),
            ]);

            if ($response->getStatusCode() == 200) {
                $response = $response->getBody();
                return Response::ok($response);
            }


        } catch (ClientException $e) {

            Log::error($uri . ' ClientException： ', $e->getMessage());
            //如果 http_errors 请求参数设置成true，在400级别的错误的时候将会抛出
            switch ($e->getCode()) {
                case 400:
                    return Response::notFoundResourceError();
                    break;
                case 401:
                    //401 配置文件中已填写access_token 初始化CoolQ对象时未传值
                    return Response::accessTokenNoneError();
                    break;
                case 403:
                    //403 验证access_token错误
                    return Response::accessTokenError();
                    break;
                case 404:
                    return Response::notFoundResourceError();
                    break;
                case 406:
                    return Response::contentTypeError();
                    break;
                default:
                    return Response::error([
                        'message' => $e->getMessage()
                    ]);
                    break;
            }
        } catch (RequestException $e) {
            //在发送网络错误(连接超时、DNS错误等)时，将会抛出 GuzzleHttp\Exception\RequestException 异常。
            //一般为coolq-http-api插件未开启 接口地址无法访问  如果docker用户可以检查是否开启端口映射
            Log::error($uri . ' RequestException： ', $e->getMessage());
            switch ($e->getCode()) {
                case 0:
                    return Response::pluginServerError();
                    break;
                default:
                    return Response::error([
                        'message' => $e->getMessage()
                    ]);
                    break;
            }
        } catch (GuzzleException $e) {
        }

    }

    public function event()
    {

        if (!$this->isHMAC()) {
            echo '{"block": true,"reply":"signature=false"}';
            return false;
        }

        $content = $this->getPutParams();

        if (empty($content)) {
            echo '{"block": true,"reply":"未接收到任何上报数据!}';
            return false;
        }

        $this->postType = $content['post_type'];
        switch ($this->postType) {
            //收到消息
            case 'message':
                $message_type = $content['message_type'];
                switch ($message_type) {
                    //私聊消息
                    case "private":

                        $this->content = [
                            'message_type' => $content['message_type'],
                            'message_id' => $content['message_id'],
                            'font' => $content['font'],
                            'user_id' => $content['user_id'],
                            'message' => $content['message'],
                            //消息子类型，如果是好友则是 "friend"，
                            //如果从群或讨论组来的临时会话则分别是 "group"、"discuss"
                            //"friend"、"group"、"discuss"、"other"
                            'sub_type' => $content['sub_type'],
                        ];

                        break;
                    //群消息
                    case "group":

                        $this->content = [
                            'message_type' => $content['message_type'],
                            'message_id' => $content['message_id'],
                            'font' => $content['font'],
                            'user_id' => $content['user_id'],
                            'message' => $content['message'],
                            'group_id' => $content['group_id'],
                            //匿名用户显示名
                            'anonymous' => $content['anonymous'],
                            //匿名用户 flag，在调用禁言 API 时需要传入
                            'anonymous_flag' => $content['anonymous_flag'],
                        ];

                        // {"reply":"message","block": true,"at_sender":true,"kick":false,"ban":false}


                        break;
                    //讨论组消息
                    case "discuss":

                        $this->content = [
                            'message_type' => $content['message_type'],
                            'message_id' => $content['message_id'],
                            'font' => $content['font'],
                            'discuss_id' => $content['discuss_id'],
                            'user_id' => $content['user_id'],
                            'message' => $content['message'],
                        ];

                        // {"reply":"message","block": true,"at_sender":true}
                        break;
                }
                break;
            //群、讨论组变动等非消息类事件
            case 'event':
                $event = $content['event'];
                switch ($event) {
                    //群管理员变动
                    case "group_admin":

                        $this->content = [
                            'event' => $content['event'],
                            //"set"、"unset"	事件子类型，分别表示设置和取消管理员
                            'sub_type' => $content['sub_type'],
                            'group_id' => $content['group_id'],
                            'user_id' => $content['user_id'],
                        ];

                        break;
                    //群成员减少
                    case "group_decrease":

                        $this->content = [
                            'event' => $content['event'],
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
                            'event' => $content['event'],
                            //"approve"、"invite"	事件子类型，分别表示管理员已同意入群、管理员邀请入群
                            'sub_type' => $content['sub_type'],
                            'group_id' => $content['group_id'],
                            'user_id' => $content['user_id'],
                            'operator_id' => $content['operator_id'],
                        ];


                        break;
                    //群文件上传
                    case "group_upload":


                        $this->content = [
                            'event' => $content['event'],
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
                            'event' => $content['event'],
                            'user_id' => $content['user_id'],
                        ];

                        break;
                }
                break;
            //加好友请求、加群请求／邀请
            case 'request':
                $request_type = $content['request_type'];
                switch ($request_type) {
                    case "friend":

                        $this->content = [
                            'request_type' => $content['request_type'],
                            'user_id' => $content['user_id'],
                            'message' => $content['message'],
                            'flag' => $content['flag'],
                        ];

                        //{"block": true,"approve":true,"reason":"就是拒绝你 不行啊"}
                        break;
                    case "group":

                        $this->content = [
                            'request_type' => $content['request_type'],
                            //"add"、"invite"	请求子类型，分别表示加群请求、邀请登录号入群
                            'sub_type' => $content['sub_type'],
                            'group_id' => $content['group_id'],
                            'user_id' => $content['user_id'],
                            'message' => $content['message'],
                            'flag' => $content['flag'],
                        ];

                        //{"block": true,"approve":true,"reason":"就是拒绝你 不行啊"}
                        break;
                }
                break;
            default:

                $this->content = $content;

                break;
        }

        $this->notify();

    }

    public function attach(BasePlugin $plugin)
    {
        $key = array_search($plugin, self::$plugins);
        if ($key === false) {
            self::$plugins[] = $plugin;
        }
        Log::debug($plugin->getPluginName() . '  插件已加载', self::$plugins);
    }

    public function detach(BasePlugin $plugin)
    {
        $key = array_search($plugin, self::$plugins);
        if ($key !== false) {
            unset(self::$plugins[$key]);
        }
        Log::debug($plugin->getPluginName() . '  插件已注销');
    }

    public function notify()
    {
        foreach (self::$plugins as $plugin) {
            // 把本类对象传给观察者，以便观察者获取当前类对象的信息
            if (!$this->block) {
                Log::debug('已通知插件 ' . $plugin->getPluginName() . ' ', $this->getContent());
                switch ($this->postType) {
                    //收到消息
                    case 'message':
                        $plugin->onMessage($this);
                        break;
                    //群、讨论组变动等非消息类事件
                    //兼容4.x
                    case 'notice':
                    case 'event':
                        $plugin->onEvent($this);
                        break;
                    //加好友请求、加群请求／邀请
                    case 'request':
                        $plugin->onRequest($this);
                        break;
                    default:
                        $plugin->onOther($this);
                        break;
                }
            } else {
                break;
            }

            if ($this->block == true) {
                Log::debug($plugin->getPluginName() . '插件已拦截后续插件', [$this->block]);
            }

        }
        $this->block = false;
    }


    public function run()
    {

        $this->event();

        //框架耗时日志记录
        $times = Time::ComMicritime(COOLQ_START, Time::getMicrotime());
        Log::debug('框架总耗时：' . $times . '秒', [$times]);
        if (getenv('APP_DEBUG') == true && getenv('LOG_LEVEL') != 'DEBUG') {
            Log::info('框架总耗时：' . $times . '秒', [$times]);
        }
    }

}