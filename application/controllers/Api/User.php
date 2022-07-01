<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class User extends RestController
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('login_model');
        $this->load->model('DevicesList_model');
//        $this->load->library('upload');
    }

    public function socialLogin_post()
    {

        $name = ucwords(strtolower($this->input->post('fname')));
        $roleId = 3;
        $email = strtolower($this->input->post('email'));
        $oauth_provider = $this->input->post('oauth_provider');
        $oauth_uid = $this->input->post('oauth_uid');
        $profileImage = $this->input->post('profile_image');


        $data = [
            'email' => $email,
            'roleId' => $roleId,
            'name' => $name,
            'createdAt' => date('Y-m-d H:i:s'),
            'oauth_provider' => $oauth_provider,
            'oauth_uid' => $oauth_uid,
            'profileImage' => $profileImage
        ];


        $user = $this->login_model->checkUser($data);

        if ($user) {
            $lastLogin = $this->login_model->lastLoginInfo($user->userId);
            $sessionArray = [
                'userId' => $user->userId,
                'role' => $user->roleId,
                'roleText' => $user->role,
                'name' => $user->name,
                'lastLogin' => ($lastLogin) ? $lastLogin->createdAt : '',
                'isLoggedIn' => true,
            ];

            $token = AUTHORIZATION::generateToken($sessionArray);

            unset($sessionArray['userId'], $sessionArray['isLoggedIn'], $sessionArray['lastLogin']);

            $loginInfo = ['userId' => $user->userId, 'sessionData' => json_encode($sessionArray), 'machineIp' => $_SERVER['REMOTE_ADDR'], 'userAgent' => getBrowserAgent(), 'agentString' => $this->agent->agent_string(), 'platform' => $this->agent->platform()];

            $this->login_model->lastLogin($loginInfo);


            $response = ['token' => $token, 'data' => $sessionArray];

            $this->response($response, self::HTTP_OK);

        } else {
            $this->response([
                'status' => false,
                'message' => 'failed logging in',
            ], self::HTTP_BAD_REQUEST);
        }

    }

    public function login_post()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[128]|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|max_length[32]');

        if (false == $this->form_validation->run()) {
            $this->response([
                'status' => false,
                'message' => 'Email and Password are required!',
            ], self::HTTP_BAD_REQUEST);
        } else {
            $email = strtolower($this->security->xss_clean($this->input->post('email')));
            $password = $this->input->post('password');

            $result = $this->login_model->loginMe($email, $password);

            if (!empty($result)) {
                $lastLogin = $this->login_model->lastLoginInfo($result->userId);

                $sessionArray = [
                    'userId' => $result->userId,
                    'role' => $result->roleId,
                    'roleText' => $result->role,
                    'name' => $result->name,
                    'lastLogin' => ($lastLogin) ? $lastLogin->createdAt : '',
                    'isLoggedIn' => true,
                ];

                $token = AUTHORIZATION::generateToken($sessionArray);

                unset($sessionArray['userId'], $sessionArray['isLoggedIn'], $sessionArray['lastLogin']);

                $loginInfo = ['userId' => $result->userId, 'sessionData' => json_encode($sessionArray), 'machineIp' => $_SERVER['REMOTE_ADDR'], 'userAgent' => getBrowserAgent(), 'agentString' => $this->agent->agent_string(), 'platform' => $this->agent->platform()];

                $this->login_model->lastLogin($loginInfo);


                $response = ['token' => $token, 'data' => $sessionArray];

                $this->response($response, self::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Email or password mismatch',
                ], self::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * This function is used to add new user to the system.
     */
    public function register_post()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('fname', 'Full Name', 'trim|required|max_length[128]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[128]');
        $this->form_validation->set_rules('password', 'Password', 'required|max_length[20]');
        $this->form_validation->set_rules('cpassword', 'Confirm Password', 'trim|required|matches[password]|max_length[20]');
        // $this->form_validation->set_rules('role', 'Role', 'trim|required|numeric');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|min_length[10]');


        if (false == $this->form_validation->run()) {
            $errors = str_replace("\n", "", strip_tags(validation_errors()));
            $this->response([
                'status' => false,
                'message' => $errors,
            ], self::HTTP_BAD_REQUEST);
        } else {
            $name = ucwords(strtolower($this->security->xss_clean($this->input->post('fname'))));
            $password = $this->input->post('password');
            $roleId = 3;
            $email = strtolower($this->security->xss_clean($this->input->post('email')));
            $mobile = $this->security->xss_clean($this->input->post('mobile'));

            $email_exists = $this->user_model->checkEmailExists($email);
            $mobile_exists = $this->user_model->checkMobileExists($mobile);

            if (!$email_exists && !$mobile_exists) {

                $userInfo = [
                    'email' => $email,
                    'password' => getHashedPassword($password),
                    'roleId' => $roleId,
                    'name' => $name,
                    'mobile' => $mobile,
                    'createdAt' => date('Y-m-d H:i:s'),
                ];

                $this->load->model('user_model');
                $result = $this->user_model->addNewUser($userInfo);

                if ($result > 0) {
                    $response = [
                        'status' => true,
                        'message' => 'User Created Successfully',
                    ];
                    $this->response($response, self::HTTP_OK);
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'User Creation failed, Please Try again',
                    ];
                    $this->response($response, self::HTTP_INTERNAL_ERROR);

                }
            } else if ($email_exists && !$mobile_exists) {

                $this->response([
                    'status' => false,
                    'message' => 'Email already Exists. Please Try to login or use different email to register',
                ], self::HTTP_BAD_REQUEST);
            } else if (!$email_exists && $mobile_exists) {

                $this->response([
                    'status' => false,
                    'message' => 'Mobile number already Exists. Please Try again',
                ], self::HTTP_BAD_REQUEST);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Mobile number  and email already Exists. Please Try again',
                ], self::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * This function used to generate reset password request link.
     */
    public function resetPasswordUser_post()
    {
        $status = '';

        $this->load->library('form_validation');

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        if (false == $this->form_validation->run()) {
            $this->response([
                'status' => false,
                'message' => validation_errors(),
            ], self::HTTP_BAD_REQUEST);
        } else {
            $email = strtolower($this->security->xss_clean($this->input->post('email')));

            if ($this->login_model->checkEmailExist($email)) {
                $encoded_email = urlencode($email);

                $this->load->helper('string');
                $data['email'] = $email;
                $data['activation_id'] = random_string('alnum', 15);
                $data['createdAt'] = date('Y-m-d H:i:s');
                $data['agent'] = getBrowserAgent();
                $data['client_ip'] = $this->input->ip_address();

                $save = $this->login_model->resetPasswordUser($data);

                if ($save) {
                    $data1['reset_link'] = base_url() . 'resetPasswordConfirmUser/' . $data['activation_id'] . '/' . $encoded_email;
                    $userInfo = $this->login_model->getCustomerInfoByEmail($email);

                    if (!empty($userInfo)) {
                        $data1['name'] = $userInfo->name;
                        $data1['email'] = $userInfo->email;
                        $data1['message'] = 'Reset Your Password';
                    }

                    $sendStatus = resetPasswordEmail($data1);

                    if ($sendStatus) {
                        $response = [
                            'status' => true,
                            'message' => 'Reset password link sent successfully, please check mails.'
                        ];
                        $status = self::HTTP_OK;
                    } else {
                        $response = [
                            'status' => false,
                            'message' => 'Email sending has been failed, try again.'
                        ];
                        $status = self::HTTP_INTERNAL_ERROR;
                    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'It seems an error while sending your details, try again.'
                    ];
                    $status = self::HTTP_INTERNAL_ERROR;
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => 'This email is not registered with us.'
                ];
                $status = self::HTTP_NOT_FOUND;
            }
            $this->response($response, $status);
        }
    }

    /**
     * This function is used to update the user details.
     *
     * @param text $active : This is flag to set the active tab
     */
    public function profileUpdate_post()
    {
        $this->load->helper('file');

        $headers = $this->input->request_headers();
        $user_data = checkJwtToken($headers);
        if ($user_data) {
            $user_id = $user_data->userId;
            $this->load->library('form_validation');

            $this->form_validation->set_rules('fname', 'Full Name', 'trim|max_length[128]');
            $this->form_validation->set_rules('mobile', 'Mobile Number', 'min_length[10]');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[128]');
            if (empty($_FILES['image']['name'])) {
                $this->form_validation->set_rules('image', 'Document');
            }

            if (false == $this->form_validation->run()) {
                $this->response([
                    'status' => false,
                    'message' => validation_errors(),
                ], self::HTTP_BAD_REQUEST);
            } else {

                $userInfo = ['updatedBy' => $user_id, 'updatedAt' => date('Y-m-d H:i:s')];
                if (!empty($_FILES['image']['name'])) {
                    $profileImage = '';
                    $fileName = $_FILES["image"]["name"];
                    $file_name = pathinfo($fileName, PATHINFO_FILENAME);
                    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

                    $config['upload_path'] = 'assets/uploads/profile_images/';
                    $config['allowed_types'] = 'jpg|png|jpeg';
                    $config['file_name'] = $user_id . "." . $fileExt;

                    if (!is_dir($config['upload_path'])) {
                        mkdir($config['upload_path'], 0777, TRUE);
                    }

                    $this->load->library('upload', $config);
                    if ($this->upload->do_upload('image')) {
                        $profileImage = $user_id . "." . $fileExt;
                    } else {
                        $error = array('error' => $this->upload->display_errors());
                        $this->response([
                            'status' => false,
                            'message' => $error
                        ], self::HTTP_BAD_REQUEST);
                    }
                    $userInfo['profileImage'] = 'assets/uploads/profile_images/' . $profileImage;
                }

                if ($this->input->post('fname')) {
                    $name = ucwords(strtolower($this->input->post('fname')));
                    $userInfo['name'] = $name;
                }
                if ($this->input->post('mobile')) {
//                    var_dump("hello");die;
                    $mobile = $this->input->post('mobile');
                    $mobile_exists = ($this->user_model->checkMobileExists($mobile, $user_id)) ? true : false;
                    if (!$mobile_exists) {
                        $userInfo['mobile'] = $mobile;
                    } else {
                        $this->response([
                            'status' => false,
                            'message' => 'Mobile number already exists'
                        ], self::HTTP_BAD_REQUEST);
                    }
                }
                if ($this->input->post('email')) {
                    $email = strtolower($this->input->post('email'));
                    $email_exists = ($this->user_model->checkEmailExists($email, $user_id)) ? true : false;
                    if (!$email_exists) {
                        $userInfo['email'] = $email;
                    } else {
                        $this->response([
                            'status' => false,
                            'message' => 'email already exists'
                        ], self::HTTP_BAD_REQUEST);
                    }
                }


                $result = $this->user_model->editUser($userInfo, $user_id);
                if ($result) {
                    $this->response([
                        'status' => true,
                        'message' => 'Profile updated successfully.'
                    ], self::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => 'Profile updation failed.'
                    ], self::HTTP_INTERNAL_ERROR);
                }


            }
        } else {
            $data = [
                'status' => false,
                'message' => 'UnAuthorized'
            ];
            $this->response($data, self::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * This function is used to show users profile.
     *
     * @param mixed $active
     */
    public function profile_get()
    {
        $headers = $this->input->request_headers();
        $user_data = checkJwtToken($headers);

        if ($user_data) {
            $data = $this->user_model->getUserInfoWithRole($user_data->userId);
            $response = [
                'status' => true,
                'data' => $data
            ];
            $this->response($data, self::HTTP_OK);
        } else {
            $data = [
                'status' => false,
                'message' => 'UnAuthorized'
            ];
            $this->response($data, self::HTTP_UNAUTHORIZED);
        }


    }

    public function updatefirebaseId_post()
    {
        $headers = $this->input->request_headers();
        $user_data = checkJwtToken($headers);

        if ($user_data) {

            $this->load->library('form_validation');

            $this->form_validation->set_rules('firebase_id', 'FirebaseId', 'required');

            if (false == $this->form_validation->run()) {
                $this->response([
                    'status' => false,
                    'message' => validation_errors(),
                ], self::HTTP_BAD_REQUEST);
            } else {

                $user_id = $user_data->userId;
                $firebaseId = $this->input->post('firebase_id');
                $userInfo = ['firebaseId' => $firebaseId, 'updatedBy' => $user_id, 'updatedAt' => date('Y-m-d H:i:s')];

                $result = $this->user_model->editUser($userInfo, $user_id);
                if ($result) {
                    $this->response([
                        'status' => true,
                        'message' => 'firebaseId updated successfully.'
                    ], self::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => 'firebaseId updation failed.'
                    ], self::HTTP_INTERNAL_ERROR);
                }
            }
        } else {
            $data = [
                'status' => false,
                'message' => 'UnAuthorized'
            ];
            $this->response($data, self::HTTP_UNAUTHORIZED);
        }
    }

    public function addDevice_post()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('deviceId', 'Device Id', 'trim|required');
        $this->form_validation->set_rules('firebaseId', 'Firebase Id', 'trim|required');

        $headers = $this->input->request_headers();
        $user_data = '';
        if (array_key_exists('Authorization', $headers)) {
            $user_data = checkJwtToken($headers);
        }

        if (false == $this->form_validation->run()) {
            $this->response([
                'status' => false,
                'message' => validation_errors(),
            ], self::HTTP_BAD_REQUEST);
        } else {

            $deviceId = $this->input->post('deviceId');
            $firebaseId = $this->input->post('firebaseId');
            $osVersion = $this->input->post('osVersion') ? $this->input->post('osVersion') : '';
            $deviceModel = $this->input->post('deviceModel') ? $this->input->post('deviceModel') : '';


            $deviceInfo = [
                'firebaseId' => $firebaseId,
                'deviceId' => $deviceId,
                'osVersion' => $osVersion,
                'deviceModel' => $deviceModel,
                'updatedAt' => date('Y-m-d H:i:s')];

            if ($user_data) {
                $deviceInfo['userId'] = $user_data['userId'];
            }


            $result = $this->DevicesList_model->getDeviceInfo($deviceId);

            if (!empty($result)) {
                $deviceInfo['isDeleted'] = 0 ;
                $result = $this->DevicesList_model->editDevice($deviceInfo, $deviceId);
//                var_dump($result);
                if ($result) {
                    $this->response([
                        'status' => true,
                        'message' => 'firebaseId updated successfully.'
                    ], self::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => 'firebaseId updation failed1.'
                    ], self::HTTP_INTERNAL_ERROR);
                }
            } else {
                $deviceInfo['createdAt'] = date('Y-m-d H:i:s');

                $result = $this->DevicesList_model->addNewDevice($deviceInfo);
                if ($result) {
                    $this->response([
                        'status' => true,
                        'message' => 'firebaseId updated successfully.'
                    ], self::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => 'firebaseId updation failed123.'
                    ], self::HTTP_INTERNAL_ERROR);
                }
            }
        }
    }

    public function deleteDevice_post()
    {

        $headers = $this->input->request_headers();
        $user_data = '';
        if (array_key_exists('Authorization', $headers)) {
            $user_data = checkJwtToken($headers);
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('deviceId', 'Device Id', 'trim|required');

        if (false == $this->form_validation->run()) {
            $this->response([
                'status' => false,
                'message' => validation_errors(),
            ], self::HTTP_BAD_REQUEST);
        } else {
            $deviceId = $this->input->post('deviceId');
            $deviceInfo = ['isDeleted' => 1, 'firebaseId' => '', 'updatedAt' => date('Y-m-d H:i:s')];

            $result = $this->DevicesList_model->editDevice($deviceInfo, $deviceId);
            if ($result) {
                $this->response([
                    'status' => true,
                    'message' => 'updated successfully.'
                ], self::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'updation failed.'
                ], self::HTTP_INTERNAL_ERROR);
            }

        }
    }
}
