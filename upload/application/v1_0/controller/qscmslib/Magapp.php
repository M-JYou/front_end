<?php
/**
 * 马甲相关
 *
 * @author andery
 */
namespace app\v1_0\controller\qscmslib;
class Magapp {

    private static $getUserInfoUrl = "{hostname}/mag/cloud/cloud/getUserInfo?token={user_token}&secret={appsecret}";
    private static $_error;
    /**
     * [get_user_info 获取马甲用户信息]
     */
    public function get_user_info($user_token){
        if(!config('global_config.magappx_secret')){
            self::$_error = '请配置马甲secret';
            return false;
        }
        if(!config('global_config.magappx_domain')){
            self::$_error = '请配置马甲客户端域名';
            return false;
        }
        $data = [
            'token' => $user_token,
            'secret' => config('global_config.magappx_secret')
        ];
        $url = str_replace('{hostname}',config('global_config.magappx_domain'),self::$getUserInfoUrl);//替换{hostname}
        $url = str_replace('{appsecret}',config('global_config.magappx_secret'),$url);//替换{appsecret}
        $url = str_replace('{user_token}',$user_token,$url);//替换{user_token}
        $reg = self::https_request(config('global_config.magappx_secret'),$url,$data);

        if(false !== $reg){
            $reg = json_decode($reg,true);
            if ($reg['success'])
            {
                $userInfo = [
                    'type' => 'magapp',
                    'id' => $reg['data']['user_id'],//用户id
                    'username' => $reg['data']['name'],//用户名
                    'signature'=> '',//个性签名
                    'gender'=>$reg['data']['sex'],//性别：0保密 1男 2女
                    'birthday'=>'',//生日
                    'avatar'=>$reg['data']['head'],//头像
                    'phone'=>$reg['data']['phone'],//手机号
                ];
                cookie('members_bind_info', $userInfo);
                return $userInfo;
            }else{
                return false;
            }
        }else{
            self::$_error = '请开启curl服务';
            return false;
        }
    }

    public function https_request($secret_key, $url, $get_params = array(), $post_data = array()){
        if(function_exists('curl_init')){
            $nonce         = rand(10000, 99999);
            $timestamp  = time();
            $array = array($nonce, $timestamp, $secret_key);
            sort($array, SORT_STRING);
            $token = md5(implode($array));
            $params['nonce'] = $nonce;
            $params['timestamp'] = $timestamp;
            $params['token']     = $token;
            $params = array_merge($params,$get_params);
            $url .= '?';
            foreach ($params as $k => $v)
            {
                $url .= $k .'='. $v . '&';
            }
            $url = rtrim($url,'&');
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, $url);
            curl_setopt($curlHandle, CURLOPT_HEADER, 0);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curlHandle, CURLOPT_POST, count($post_data));
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $post_data);
            $data = curl_exec($curlHandle);
            $status = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
            curl_close($curlHandle);
            return $data;
        }else{
            return false;
        }
    }
    /**
     * 错误
     */
    public function getError(){
        return self::$_error;
    }
}