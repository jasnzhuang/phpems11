<?php
namespace PHPEMS;
class app
{
    public $user;
    public $session;

    public function __construct()
    {
        $this->session = M('session')->getSessionUser();
        $this->user = M('user','user')->getUserById($this->session['sessionuserid']);
        if($this->user['groupid'] != 1 && M('ev')->url(2) != 'login')
        {
            $message = array(
                'statusCode' => 300,
                "message" => "请您重新登录",
                "callbackType" => 'forward',
                "forwardUrl" => "index.php?core-master-login"
            );
            R($message);
        }
        $localapps = M('apps','core')->getLocalAppList();
        $apps = M('apps','core')->getAppList();
        if(!in_array(ginkgo::$app,$this->user['manager_apps']) && $apps['user']['appsetting']['managemodel'])
        {
            header("location:index.php?core-master");
            exit();
        }
        M('tpl')->assign('localapps',$localapps);
        M('tpl')->assign('apps',$apps);
        M('tpl')->assign('_user',$this->user);
    }

    public function plugin()
    {
        $module = M('ev')->url(1)?:'app';
        $plugin = M('ev')->url(2)?:'demo';
        $controller = M('ev')->url(3)?:'index';
        $class = "\\PHPEMS\\plugins\\{$plugin}\\{$controller}";
        $file = PEPATH."/plugins/{$plugin}/{$controller}.{$module}.php";
        if(!file_exists($file))R(array(
            'statusCode' => 300,
            "message" => "需要引用的文件不存在",
            "callbackType" => 'forward',
            "forwardUrl" => "index.php"
        ));
        include_once $file;
        $action = new $class();
        $action->display();
    }
}

?>