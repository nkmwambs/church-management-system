<?php

namespace App\Controllers;

use App\Libraries\UserLibrary;;
use CodeIgniter\HTTP\RedirectResponse;

class Login extends BaseController {
    protected $session;

    function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger){
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
    }

    public function index(): string|RedirectResponse
    {
        if($this->session->get('user_is_authenticated')){
            return redirect()->to('/dashboard');
        }
        return view('signin');
    }

    public function ajax_login(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Initialize an empty response array
        $response = array();

        // Get the email and password from the POST request
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Add the submitted data to the response array
        $response['submitted_data'] = $this->request->getPost();

        // Validate the login credentials
        $login_status = $this->confirm_login(strtolower(trim($email)), $password);

        // Add the login status to the response array
        $response['login_status'] = $_POST;

        // If the login status is 'success', add an empty redirect URL to the response array
        if ($login_status == 'success') {
            $response['login_status'] = $login_status;
            $response['redirect_url'] = base_url().'dashboard';
        }
        
        // Send the JSON response to the client
        return $this->response->setJSON($response);
    }

    public function confirm_login($userEmail, $password){

        $user = new UserLibrary();
        $userData = $user->getUserByEmailAndPassword($userEmail, $password);
        // log_message('error', json_encode($userData));

        if(!empty($userData)){
             return $this->create_user_session($userData);
        }else{
            return 'invalid';
        }
    }

    private function create_user_session($userData){
        $denominationModel = new \App\Models\DenominationModel();
        $denomination = $denominationModel->find($userData['denomination_id']);
        // log_message('error', json_encode($denomination));
        $user_session = [
            'user_is_authenticated' => 1,
            'full_name' => $userData['first_name'].' '.$userData['last_name'],
            'denomination_id' => $userData['is_system_admin'] == 'no' ? $userData['denomination_id'] : 0,
            'denomination_code' => $userData['is_system_admin'] == 'no' ? $denomination['code']: 'GLOBAL',
            'user_id' => $userData['id'],
            'role_ids' => $userData['is_system_admin'] == 'yes' ? ['*'] : explode(',',$userData['roles']), // Override given roles if a system admin
            'system_admin' => $userData['is_system_admin'] ==  'yes' ? true : false,
        ];

        if ($this->session->has('user_is_authenticated')) {
            $this->session->remove(array_keys($user_session));
        }

        // Set the user authentication status and user session data
        $this->session->set('user_is_authenticated', 1);
        $this->session->set($user_session);

        $this->updateUserLoginData($userData['id']);

        return 'success';
    }

    private function updateUserLoginData($userId){
        $user = new UserLibrary();
        $user->updateUserLoginData($userId);
    }

    public function logout(): RedirectResponse
    {
        // Destroy the user session
        $this->session->destroy();

        // Redirect the user to the login page
        return redirect()->to('/');
    }
}