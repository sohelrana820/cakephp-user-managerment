<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use App\Model\Table\ProjectsUsersTable;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\Network\Session;
use Cake\Routing\Router;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @property \App\Model\Table\ProjectsUsersTable $ProjectsUsers;
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */

    // $mode = live | demo
    public $mode = 'demo';

    /**
     * @var
     */
    public $userId;

    /**
     * @var
     */
    public $loggedInUser;

    /**
     * @var string
     */
    public $appsName = 'Task Manager';

    /**
     * @var string
     */
    public $appsLogo = 'logo.png';

    /**
     * @var null
     */
    public $baseUrl = null;

    /**
     * @var string
     */
    public $emailFrom = 'info@task-manager.com';

    /**
     * @var string
     */
    public $currentTheme = 'Apps';

    /**
     * @var int
     */
    public $paginationLimit = 50;

    public function initialize()
    {
        parent::initialize();
        I18n::locale('en_US');
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Utilities');
        $this->loadComponent('Email');
        $this->loadComponent('Auth', [
            'loginRedirect' => [
                'controller' => 'Dashboard',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login',
            ]
        ]);
        $this->loadModel('Users');
        $this->loadModel('Labels');
        $this->loadModel('Tasks');
    }

    /**
     * @param Event $event
     * @return \Cake\Network\Response|null
     */
    public function beforeFilter(Event $event)
    {
        $this->Auth->allow([
            'signup',
            'verifyEmail',
            'forgotPassword',
            'install',
            'resetPassword',
        ]);
        $this->userId = $this->Auth->user('id');
        $this->baseUrl = Router::url('/', true);
        $this->loggedInUser = $this->Users->getUserByID($this->userId);

        $this->viewBuilder()
            ->layout('application')
            ->theme($this->currentTheme);
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }

        $this->set('userInfo', $this->loggedInUser);
        $this->set('baseUrl', $this->baseUrl);
        $this->set('title', $this->appsName);
        $this->set('appsName', $this->appsName);
        $this->set('appsLogo', $this->appsLogo);
    }

    /**
     * @return \Cake\Network\Response|null
     */
    public function checkAuthentication()
    {
        if ($this->Auth->user()) {
            return $this->redirect($this->referer());
        }
    }

    /**
     * @return bool
     */
    protected function isAdmin()
    {
        if ($this->Auth->user('role') == 1) {
            return true;
        }
        return false;
    }

    /**
     * @param $permission
     */
    protected function checkPermission($permission)
    {
        if (!$permission) {
            $this->Flash->error(_('Sorry, you are not authorised to access this page'));
            $this->redirect($this->referer());
        }
    }
}
