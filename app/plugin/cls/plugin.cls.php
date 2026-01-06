<?php

namespace PHPEMS\plugin;
use function \PHPEMS\M;
use function \PHPEMS\P;

class plugin
{
    /**
     * 已注册的监听器
     * @var array
     */
    private $_hooks = array();
    private $_filters = array();
    private $isLoad = 0;
    /**
     * 插件目录
     * @var string
     */
    private $_pluginDir = PEPATH.'/plugins/';

    /**
     * 注册插件钩子
     * @param string $hook 钩子名称
     * @param mixed $listener 监听器回调
     */
    public function registerHook($hook, $listener, $level = 10)
    {
        if (!isset($this->_hooks[$hook])) {
            $this->_hooks[$hook] = array();
        }
        $this->_hooks[$hook][$level][] = $listener;
    }

    //获取本地应用列表
    public function getLocalPlugins()
    {
        return M('files')->listDir($this->_pluginDir);
    }

    public function registerFilter($filter, $listener, $level = 10)
    {
        if (!isset($this->_filters[$filter])) {
            $this->_filters[$filter] = array();
        }
        $this->_filters[$filter][$level][] = $listener;
    }

    /**
     * 触发钩子执行
     * @param string $hook 钩子名称
     * @param mixed $data 传递的数据
     */
    public function trigger($hook, $data = null)
    {
        if(!$this->isLoad)$this->loadPlugins();
        if (isset($this->_hooks[$hook])) {
            foreach ($this->_hooks[$hook] as $listeners) {
                ksort($listeners);
                foreach($listeners as $listener)
                {
                    if (is_callable($listener)) {
                        call_user_func($listener, $data);
                    }
                }
            }
        }
    }

    public function filter($filter, $data = null)
    {
        if(!$this->isLoad)$this->loadPlugins();
        if (isset($this->_filters[$filter])) {
            foreach ($this->_filters[$filter] as $listeners) {
                ksort($listeners);
                foreach($listeners as $listener)
                {
                    if (is_callable($listener)) {
                        $data = call_user_func($listener, $data);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 加载所有活跃插件
     */
    public function loadPlugins()
    {
        $plugins = $this->getActivePlugins();
        foreach ($plugins as $plugin) {
            P('hooks',$plugin)->register();
            P('filters',$plugin)->register();
        }
        $this->isLoad = 1;
    }

    /**
     * 获取活跃插件列表（示例实现）
     * @return array
     */
    public function getActivePlugins()
    {
        $data = [
            'select' => 'plugin',
            'table' => 'plugins',
            'query' => [
                ["AND","pluginstatus = 1"]
            ],
            'limit' => false
        ];
        $rs = M('pepdo')->getElements($data);
        $plugins = array();
        foreach($rs as $r)
        {
            if(is_dir($this->_pluginDir.$r['plugin']))
            {
                $plugins[] = $r['plugin'];
            }
        }
        return $plugins;
    }
	
	public function getInstalledPlugins()
    {
        $data = [
            'select' => 'plugin',
            'table' => 'plugins',
            'query' => [],
            'limit' => false
        ];
        $rs = M('pepdo')->getElements($data);
        $plugins = array();
        foreach($rs as $r)
        {
            if(is_dir($this->_pluginDir.$r['plugin']))
            {
                $plugins[] = $r['plugin'];
            }
        }
        return $plugins;
    }

    public function modifyPlugin($plugin,$args = array())
    {
        $data = [
            'select' => 'plugin',
            'table' => 'plugins',
            'query' => [
                ["AND","plugin = :plugin","plugin",$plugin]
            ],
            'value' => $args
        ];
        return M('pepdo')->updateElement($data);
    }

    public function installPlugin($plugin)
    {
        if(is_dir(PEPATH.'/plugins/'.$plugin))
        {
            if(method_exists(P('config',$plugin),'install'))
            {
                if(!P('config',$plugin)->install())
                {
                    return false;
                }
            }
            $args = [
                'plugin' => $plugin,
                'pluginstatus' => 1
            ];
            return M('pepdo')->insertElement([
                'table' => 'plugins',
                'query' => $args
            ]);
        }
        else return false;
    }

    public function uninstallPlugin($plugin)
    {
        if(is_dir(PEPATH.'/plugins/'.$plugin))
        {
            if(method_exists(P('config',$plugin),'uninstall'))
            {
                if(!P('config',$plugin)->uninstall())
                {
                    return false;
                }
            }
            $args = [
                'pluginstatus' => 0
            ];
            return $this->modifyPlugin($plugin,$args);
        }
        else return false;
    }

    public function getPluginByName($plugin)
    {
        $data = [
            'table' => 'plugins',
            'query' => [
                ["AND","plugin = :plugin","plugin",$plugin]
            ],
            'serial' => 'pluginsetting',
            'limit' => 1
        ];
        return M('pepdo')->getElement($data);
    }


}