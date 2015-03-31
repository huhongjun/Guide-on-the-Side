<?php

App::uses('AuthComponent', 'Controller/Component');

class FlexAuthComponent extends AuthComponent {
    
    // Why isn't this available somewhere?
    public $pluginName = 'FlexAuth';

    // Default to /admin after login unless they were trying to go somewhere
    //   else, like an unpublished tutorial.
    public $loginRedirect = '/admin';

    function initialize(Controller $controller) {
        $controller->Auth = $this; 
        
        $this->loginAction = array('plugin' => 'flex_auth', 'controller' => 'flex_auth', 'action' => 'login');
        
        $userAuthMethod = Configure::read('user_config.authentication.method');
        if (!empty($userAuthMethod)) {
            $this->authenticate = array($this->pluginName . '.' . ucfirst($userAuthMethod));
            $logoutRedirect = Configure::read("user_config.$userAuthMethod.logout_url");
            if (!empty($logoutRedirect)) {
                $this->logoutRedirect = str_replace(array('%HOST%'), array($_SERVER['SERVER_NAME']), $logoutRedirect) . 
                    '?return=' . Router::url(array('plugin' => 'flex_auth', 'controller' => 'flex_auth', 'action' => 'login'), 
                    true);
            }
        }
        
        parent::initialize($controller);
    }
    
    function isLoginDataAvailable() {
        $this->constructAuthenticate();
        foreach ($this->_authenticateObjects as $auth) {
            if ($auth->isLoginDataAvailable($this->request)) {
                return true;
            }
        }
    }
}

