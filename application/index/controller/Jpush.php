<?php
/**
 * User: lilu
 * Date: 2019/5/28
 * Time: 下午3:12
 */

namespace app\idnex\controller;


require EXTEND_PATH.'jpush//autoload.php';

class JPush extends Controller
{
    /**
     * 通过别名发送极光推送消息
     * @param $title // 标题
     * @param $content // 内容
     * @param $alias // 别名
     * @param array $params // 扩展字段
     * @param string $ios_badge // ios 角标数
     * @param array $platform // 推送设备
     * @return array|bool
     * @author huangzhicheng 2018年08月29日
     */
    public static function pushMessageByAlias ($title, $content, $alias, $params = [], $ios_badge = '0', $platform = ['ios', 'android'])
    {

        if (!is_array ($alias)) return false;
        $jpush_conf = Output::getJPushKey (); // 获取配置信息 app_key 和 master_secret

        $app_key = $jpush_conf[ 'app_key' ];
        $master_secret = $jpush_conf[ 'master_secret' ];
        try {
            // 初始化
            $client = new Client($app_key, $master_secret);

            $result = $client->push ()
                ->setPlatform ($platform)
                ->addAlias ($alias)
                ->iosNotification (
                    $content, [
                    'sound' => '1',
                    'badge' => (int)$ios_badge,
                    'content-available' => true,
                    'category' => 'jiguang',
                    'extras' => $params,
                ])
                ->androidNotification ($content, [
                    'title' => $title,
                    //'build_id' => 2,
                    'extras' => $params,
                ])
                ->options ([
                    'sendno' => 100,
                    'time_to_live' => 86400,
                    'apns_production' => true, // ios推送证书的选择，True 表示推送生产环境，False 表示要推送开发环境
                    //'big_push_duration' => 10,
                ])
                ->send ();
            return $result;
        } catch (\Exception $e) {
            // 写入错误日志
            // 这里根据自己的业务来定
        }
    }
}