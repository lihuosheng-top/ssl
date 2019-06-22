<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用命名空间
    'app_namespace'          => 'app',
    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],
    // //模板常量配置
    // 'tpl_replace_string'  =>  [
    //     '__STATIC__'=>'./static',
    //     '__PUBLIC__'=>'/public'
    // ],
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__PUBLIC__'=>'/uploads',
    ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
    //app配置
    'app_info'=>[
        'app_id'=>'1231231',
        'app_secret'=>'1222eed1ee2e',
    ],
    //redis配置
    'redis' =>  [
        'type'  =>  'redis',
        'host'  =>  '127.0.0.1',
        // 全局缓存有效期（0为永久有效）
        'expire'=>  0,
        // 缓存前缀
        'prefix'=>  'think',
    ],
    //alipay----app
    'alipay'=>[
        'appId'             => '2019042464293493',
        'gatewayUrl'        => 'https://openapi.alipay.com/gateway.do',
        'rsaPrivateKey'     => 'MIIEowIBAAKCAQEAoYWDp2PoQcpdrUlb+ceU8rocTWqLuZqLU/xwSQF56JeXz92e/OxhrlEAhYjJ6KSLC3424A5gXi8Iv6Jh+pwe7hZAYsRV6CkaLIwzN4X+ODiK1zvOSQq2RWMa44/5fTsYsOFBmKC85W8nDiZ7wRyOGS/7fCJJMZ+SlQXS/31Ms+2eQK82iVYoLKE+d8G3l8tV8sUkMxP/3B7xf0LdsTH4/ed4KcG5s6cQ5qvq0S0sWGy3A8gDZmskGOWqE3g2omgtc4t2cFnJwW7Nu+JZ/KVlKvnJLkn/FLAL0PYYs3hlzjehQOhHW43M2zoOKY6VJPCbN4JwFE3oQGwwC0mcK3jzzwIDAQABAoIBAFCLjOhnKMXODULBh00amoskBcMRM5YdnAcIplGxm8JB/iFZCdRkaB2OJS1x1gXeL7xOdzzpzmKJH9q4NhXLzpy5mefmslQwUu4OB+hTOD9+6rWEDE1eOyCE2D7MaARr+NcTrStYVHoxII+s3x+ghrfGtqjjTA6j3WvU2LAxIpPCSns1zunDIsXVGdE1cIZ7YY6nHWgkTNVX6GtnaAx0P/DcgeL7KTn2BB/JqckFaiMo/mOiysLwSzhN+FV30X3kgW+XDhZmZUh2aLDCvT7GtRZWbZnPMbDNGqoLWGa+83qY1v8ZeySmhObr/AYIEJ64tlSvbANHdm2rwahmlKbWr3ECgYEA1zpuhpmYewisYr+SDQ41gPQQKLt7cnxA3+bo2uuJ3jXokABTN6SNbKrNgNbliKb0byor/ffETMAa2fzj/KHiYWM3drQhfhpMYUph4xEO0Uiz80rT7+5UfmVe+xPmcinsihK+90G6kS1FQlTV9QbHNqsrwGUmM6mpUI1/gqkQwncCgYEAwB6OoKo4U28MJkA5ndUbWarQniGkt7FX9uuY8KOcXgN4b0RIqevzQ/rWo4wKT7E52lHna+LTsYPhuGGW5+dMqJIDKW+nSbob77plJlfKN0E5V4UYVHF3AydQsN13URa2OM8jxdmYQHxXTORpHMVQVupHB6WKOCHnE3DIyIKDl2kCgYBIJqx6ru7w/91aVUJO4h6Jlkf8f61ImeLdrHkPOrrDJWtLKvC6zI0kirXNpTzZbK6wvYB16SODXzshLKf5PwO3Bh3Hy2cW8RX934OlLUi8XBUHUGRwIINuw+jtXqR4TRmgx9wA46X5mSWIsQvw0k7iQsut+bquwwehm23NjYOyzQKBgEnp97m8g5sl+CinQQOGP9WJsGWGUBcUNeGb1uWOLNTGxQkZ3lLxolAfK4EUmcr7RmTGaiDJzeRX9vm3IhXqmUWpl2nTuSrWXXUn406TLAuCHoubezbRyJYABD1nl0YtVMIaf4rZIoh8ziGZJw+YokYuMFGy0LCz6nFCTR1HiVUxAoGBAMIwZKz+lrAnkD9kokW1OelYu/ewoQ6/M1s39LbZFbiMpS+fL2MjTNdZTDQj2wtjcDfRd5MFmcPiKHC6FpznWXZGTH6FPBKTT1JV+tmNvlHjlN4WAFm0tUn7W4zeMvmv8G/gyp4yjMK7ZKLINL+3Yi7/LtDmYgmI/G5xiZZTWv/t',
        // 'rsaPublicKey'      => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAniRtd+MDzmDOrGJ8HWf/sNFp+4i1U+54KTo9SDGGSTj9eocbNX/VfcT/eCWDE09UrKMUZtLxNQudH7BI5p/0Z4v8/TL8awaDzi8l7jRpC+VWifNLEMEbOYVKFdQL/TyDfCnd0m/hC4vdWU6oHOtqaAjVE4PafUs6r4ivfll0RMxnjDjfCCrQU6r6krJfHUQ3PNSED3l4qHVY6aI1ZP3RxBsg246+T3UvFFS9qdggGJfhG/gWoLaAIxqGFXeWkOah1tPmjhHhwj1RAJs9HqDh/U4Tcr/tZ6KtwsAK++SbkdIFxdsePzvGl2wobsCFno2eto8WlMFp6luMb4jH6KtQ1wIDAQAB',
        'alipayrsaPublicKey'=> 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhxrWOWju6mT8lfaa7KQvxa56MFKgb+SYqbzCI4QlNm8XijjaHLxNixTLDVm3hFgnfX8NHsuHGTdBqNAko4LJ4NNCD9AIyPUL2fVTx/AVdIEz7BVq4agR1bwMc0eYDQYgRc1s3HiZIMdbbdEP3KMHti5HwtYp4WOI+Vv2UF3j4gwd/9etB+edAJ/fAunwZHEhYdX84L7Hq+rIBJN/s+MnAKcvS11B2C+mqphtt4C99HjLdbpKJK4PnYMMmxVfSuNytWddUaT3/gWMPTNU8zky8UrlWNuk6z8Cl54XTJKH2VM5Cj4xMR3mjYXwR3U41jjk9Fd/VobpA3QIpc5ehUPbOQIDAQAB',
        'seller'            => '',//可不要
        'format'            => 'json',
        'charset'           => 'UTF-8',
        'signType'          => 'RSA2',
        'transport'         => 'http',
        'version'           => '1.0',
    ],

];
