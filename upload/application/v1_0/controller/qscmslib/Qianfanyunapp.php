<?php
/**
 * 千帆相关
 *
 * @author andery
 */
namespace app\v1_0\controller\qscmslib;
class Qianfanyunapp {

    private static $getUserInfoUrl = "{hostname}/openapi/users/{user_id}?expand=groups";
    private static $loginUrl = "{hostname}/openapi/users/parse-wap-token";
    private static $_error;

    public function is_login(){
        if(!config('global_config.qianfan_token')){
            self::$_error = '请配置千帆token';
            return false;
        }
        if(!config('global_config.qianfan_domain')){
            self::$_error = '请配置千帆客户端域名';
            return false;
        }
        $data['wap_token'] = $_COOKIE['wap_token'];
        $reg = $this->request_post(str_replace('{hostname}',config('global_config.qianfan_domain'),self::$loginUrl),$data);
        if(false !== $reg){
            if($reg['code'] == 0){
                return $reg['data']['uid'];
            }
            return false;
        }else{
            self::$_error = '请开启curl服务';
            return false;
        }
    }
    /**
     * [get_user_info 获取千帆用户信息]
     */
    public function get_user_info($uid){
        if(!config('global_config.qianfan_token')){
            self::$_error = '请配置千帆token';
            return false;
        }
        if(!config('global_config.qianfan_domain')){
            self::$_error = '请配置千帆客户端域名';
            return false;
        }
        $data['user_ids'] = $uid;
        $url = str_replace('{hostname}',config('global_config.qianfan_domain'),self::$getUserInfoUrl);
        $url = str_replace('{user_id}',$uid,$url);
        $reg = $this->request_get($url);
        if(false !== $reg){
            if ($reg['code'] == 0 && $uid == $reg['data']['id'])
            {
                $userInfo = [
                    'type' => 'qianfanyunapp',
                    'id' => $reg['data']['id'],//用户id
                    'username' => $reg['data']['username'],//用户名
                    'signature'=> $reg['data']['signature'],//个性签名
                    'gender'=>$reg['data']['gender'],//性别：0保密 1男 2女
                    'birthday'=>$reg['data']['birthday'],//生日
                    'avatar'=>$reg['data']['avatar'],//头像
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
    /**
     * 错误
     */
    public function getError(){
        return self::$_error;
    }

    private function request_post($url,$data=[]){
        $data  = json_encode($data);
        $headerArray = [
            "Content-type:application/json;charset='utf-8'",
            "Accept:application/json",
            "Authorization: Bearer ".config('global_config.qianfan_token')
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headerArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return json_decode($output,true);
    }

    private function request_get($url){
        $headerArray = [
            "Content-type:application/json;charset='utf-8'",
            "Accept:application/json",
            "Authorization: Bearer ".config('global_config.qianfan_token')
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output,true);
        return $output;
    }

}