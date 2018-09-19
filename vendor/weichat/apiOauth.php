<?php 

class apiOauth{
    public $appId;      //微信appId
    public $appSecret;  //微信appSecret
    public $encodingAesKey; //微信加密encodingAesKey

    function __construct($appId,$appSecret){
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /*获取用户openid 与 网页授权access_token
    $scope snsapi_base(不弹出授权页面，直接跳转，只能获取用户openid)，snsapi_userinfo(弹出授权页面，可通过openid拿到昵称、性别、所在地。)。默认为snsapi_base。
    */
    public function getOpenid($scope=''){
        $redirect_uri 	= "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $codeUrl 		= $this->get_code_url($redirect_uri,$scope);
        if(empty($_GET['code']) && empty($_GET['state'])){
            header("Location: $codeUrl");
            exit;
        }else{
            $code 	= $_GET['code'];
            if(!empty($code)){
                $res = $this->get_web_access_token($code);
                if(empty($res['errcode'])){
                    $data 	= array(
                        'access_token'	=> $res['access_token'],
                        'openid'		=> $res['openid'],
                    );
                    return $data;
                }else{
                    header("Location: $redirect_uri");
                    exit('授权错误，请检查公众号权限和设置');
                }
            }
        }
    }


    /*组装code的url*/
    public function get_code_url($redirect_uri ='',$scope='snsapi_base', $state = 'oauth'){
        $redirect_uri = urlencode($redirect_uri);
        $url 	=  "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appId."&redirect_uri=".$redirect_uri."&response_type=code&scope=".$scope."&state=".$state;
        $url .= '#wechat_redirect';
        return $url;

    }
    /*获取网页授权access_token
    $code  微信返回的code
    */
    public function get_web_access_token($code){
        $tokenUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appId."&secret=".$this->appSecret."&code=".$code."&grant_type=authorization_code";
        $res 	= $this->https_request($tokenUrl);
        return $res;

    }
    /*发送请求*/
    public function https_request($url, $data = null){
        $curl = curl_init();
        $header = array("Accept-Charset: utf-8");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        //curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $errorno= curl_errno($curl);
        if ($errorno) {
            return array('curl'=>false,'errorno'=>$errorno);
        }else{
            $res = json_decode($output,1);
            if (isset($res['errcode']) && $res['errcode']){
                return array('errcode'=>$res['errcode'],'errmsg'=>$res['errmsg']);
            }else{
                return $res;
            }
        }
        curl_close($curl);
    }

}
?>