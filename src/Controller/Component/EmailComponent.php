<?php
/**
 * @Author: Preview ICT Ltd.
 * @URI: http://previewict.com
 * @description: This component is creating doing the some extra work.
 */

namespace App\Controller\Component;

use Aura\Intl\Exception;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use App\Controller\AppController;
use Cake\Routing\Router;

class EmailComponent extends Component
{
    public $name = 'Email';

    protected $configuration;

    /**
     * @param $data
     * @param $code
     */
    public function signupConfirmEmail($data, $code)
    {
        $app = new AppController();
        $subject = 'Create Account Confirmation - ' . $app->appsName;

        $email = new Email();
        $link = Router::url('/', true) . '/users/verify_email?code=' . $code;
        $user = array(
            'to' => $data['username'],
            'name' => $data['profile']['first_name'] . ' ' . $data['profile']['last_name']
        );

        $data = array(
            'user' => $user,
            'appName' => $app->appsName,
            'link' => $link
        );

        try {
            $email->from([$app->emailFrom => $app->appsName])
                ->to($user['to'])
                ->subject($subject)
                ->theme($app->currentTheme)
                ->template('signup_confirmation')
                ->emailFormat('html')
                ->set(['data' => $data])
                ->send();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param $data
     * @param $code
     * @return bool
     */
    public function forgotPassEmail($data, $code)
    {
        $app = new AppController();
        $subject = 'Forgot Password Link - ' . $app->appsName;
        $email = new Email();
        $link = Router::url('/', true) . 'users/reset_password?code=' . $code;
        $user = array(
            'to' => $data['username'],
            'name' => $data['profile']['first_name'] . ' ' . $data['profile']['last_name']
        );

        $data = array(
            'user' => $user,
            'appName' => $app->appsName,
            'link' => $link
        );

        try {
            $email->from([$app->emailFrom => $app->appsName])
                ->to($user['to'])
                ->subject($subject)
                ->theme($app->currentTheme)
                ->template('forgot_password')
                ->emailFormat('html')
                ->set(['data' => $data])
                ->send();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param $data
     * @return bool
     */
    public function passwordChangedEmail($data)
    {
        $app = new AppController();
        $subject = 'Password Changed - ' . $app->appsName;
        $email = new Email();

        $user = array(
            'to' => $data['username'],
            'name' => $data['profile']['first_name'] . ' ' . $data['profile']['last_name']
        );

        $data = array(
            'user' => $user,
            'appName' => $app->appsName,
        );

        try {
            $email->from([$app->emailFrom => $app->appsName])
                ->to($user['to'])
                ->subject($subject)
                ->theme($app->currentTheme)
                ->template('changed_password')
                ->emailFormat('html')
                ->set(['data' => $data])
                ->send();
        } catch (\Exception $e) {
            return null;
        }
    }
}
