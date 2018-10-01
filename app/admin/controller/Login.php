<?php
namespace app\admin\controller;

use app\admin\model\User;
use think\Controller;
use think\Request;

class Login extends Controller
{
    public function index()
    {
       
        return view();
    }
    public function login(Request $request)
    {
        $username= $request->post('username');//获取用户名
        $password= $request->post('password');//获取密码
        $user = new User();
        //验证登入
        if(!$user->checkLogin($username,$password)){

            $this->error($user->getError());
        }
        //记录日志
        $operation_obj = new \Net\Operation();
        $operation_obj->writeLog();

        $this->success('登录成功！', url('Index/index'));
    }

    /* 退出登录 */
    public function out(){
        $operation_obj = new \Net\Operation();
        $operation_obj->writeLog();
        if(isAdminLogin()){
            session('uid', null);
            session('info', null);
            session('admin_login', null);
            session('admin_login_sign', null);
            $this->redirect('Login/index');
        } else {
            $this->redirect('Login/index');
        }
    }
    /**
     * 定义方法
     *
     * 重写thinkphp跳转成功方法
     *
     * @param string $message
     * @param string $uri
     * @param bool|mixed $param
     */
    public function success_new($message,$uri="",$param = [])
    {
        if ($uri) {
            $param_str = '';
            if($param) {
                foreach ($param as $k => $v) {
                    $param_str .= '/' . $k . '/' . $v;
                }
                echo '<script>alert(\'' . $message . '\');location=\'' . $uri . $param_str . '\'</script>';
            }else{
                echo '<script>alert(\'' . $message . '\');location=\'' . $uri .  '\'</script>';
            }
        } else {
            echo '<script>alert(\''.$message.'\');history.go(-1)</script>';
        }
    }
	
	/**
	 * [http 调用接口函数]
	 * @Date   2016-07-11
	 * @Author GeorgeHao
	 * @param  string       $url     [接口地址]
	 * @param  array        $params  [数组]
	 * @param  string       $method  [GET\POST\DELETE\PUT]
	 * @param  array        $header  [HTTP头信息]
	 * @param  integer      $timeout [超时时间]
	 * @return [type]                [接口返回数据]
	 */
	function http($url, $params, $method = 'GET')
	{
		// POST 提交方式的传入 $set_params 必须是字符串形式
		$opts = array(
			//CURLOPT_TIMEOUT => $timeout,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			//CURLOPT_HTTPHEADER => $header
		);

		/* 根据请求类型设置特定参数 */
		switch (strtoupper($method)) {
			case 'GET':
				$opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
				break;
			case 'POST':
				$params = http_build_query($params);
				$opts[CURLOPT_URL] = $url;
				$opts[CURLOPT_POST] = 1;
				$opts[CURLOPT_POSTFIELDS] = $params;
				break;
			case 'DELETE':
				$opts[CURLOPT_URL] = $url;
				$opts[CURLOPT_HTTPHEADER] = array("X-HTTP-Method-Override: DELETE");
				$opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
				$opts[CURLOPT_POSTFIELDS] = $params;
				break;
			case 'PUT':
				$opts[CURLOPT_URL] = $url;
				$opts[CURLOPT_POST] = 0;
				$opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
				$opts[CURLOPT_POSTFIELDS] = $params;
				break;
			default:
				throw new Exception('不支持的请求方式！');
		}
	  
		/* 初始化并执行curl请求 */
		$ch = curl_init();
		curl_setopt_array($ch, $opts);
		$data = curl_exec($ch);
		$error = curl_error($ch);
		return $data;
	}
}
