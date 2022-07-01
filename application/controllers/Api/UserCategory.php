<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class UserCategory extends RestController
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('UserCategories_model');
        $this->load->model('Categories_model');
    }

    public function addusercategories_post()
    {
        $headers = $this->input->request_headers();
        $user_data = checkJwtToken($headers);
        if ($user_data) {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('categoriesId[]', 'CategoriesIDs', 'required');

            if (false == $this->form_validation->run()) {
                $errors = str_replace("\n", "", strip_tags(validation_errors()));
                $this->response([
                    'status' => false,
                    'message' => $errors,
                ], self::HTTP_BAD_REQUEST);
            } else {
                $userId = $user_data->userId;
                $categoriesId = json_encode($this->input->post('categoriesId'));

                $usercategoryInfo = ['userId' => $userId, 'categoriesId' => $categoriesId, 'createdAt' => date('Y-m-d H:i:s'),];
                $result = $this->UserCategories_model->addUsercategory($usercategoryInfo);
                if ($result > 0) {
                    $response = [
                        'status' => true,
                        'message' => 'userCategories Created Successfully',
                    ];
                    $this->response($response, self::HTTP_OK);
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'userCategories Creation failed, Please Try again',
                    ];
                    $this->response($response, self::HTTP_INTERNAL_ERROR);
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

    public function updateusercategories_post()
    {
        $headers = $this->input->request_headers();
        $user_data = checkJwtToken($headers);
        if ($user_data) {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('categoriesId[]', 'Categories IDs', 'trim|required');

            if (false == $this->form_validation->run()) {
                $errors = str_replace("\n", "", strip_tags(validation_errors()));
                $this->response([
                    'status' => false,
                    'message' => $errors,
                ], self::HTTP_BAD_REQUEST);
            } else {

                $user_categories = $this->UserCategories_model->getUserCategories($user_data->userId);
                $id = $user_categories->id;

                $userId = $user_data->userId;
                $categoriesId = json_encode($this->input->post('categoriesId'));

                $usercategoryInfo = ['userId' => $userId, 'categoriesId' => $categoriesId, 'updatedAt' => date('Y-m-d H:i:s'),];
                $result = $this->UserCategories_model->editUserCategories($usercategoryInfo, $id);

                if ($result > 0) {
                    $response = [
                        'status' => true,
                        'message' => 'userCategories Updated Successfully',
                    ];
                    $this->response($response, self::HTTP_OK);
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'userCategories Updation failed, Please Try again',
                    ];
                    $this->response($response, self::HTTP_INTERNAL_ERROR);
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

    public function usercategories_get()
    {
        $headers = $this->input->request_headers();
        $user_data = checkJwtToken($headers);
        if ($user_data) {
            $user_categories = $this->UserCategories_model->getUserCategoriesInfo($user_data->userId);

            $categories = [];
            foreach ($user_categories as $cat){
                $categories[] = $this->Categories_model->getCategoryInfo((int)$cat);
            }
            if ($user_categories) {
                $response = [
                    'status' => true,
                    'data' => $categories,
                ];
                $this->response($response, self::HTTP_OK);
            } else {
                $response = [
                    'status' => false,
                    'message' => 'No categories selected by user',
                ];
                $this->response($response, self::HTTP_OK);
            }


        } else {
            $data = [
                'status' => false,
                'message' => 'UnAuthorized'
            ];
            $this->response($data, self::HTTP_UNAUTHORIZED);
        }
    }

    public function userLikedCategoryImages_get()
    {
        $headers = $this->input->request_headers();
        $user_data = checkJwtToken($headers);
        if ($user_data) {
            $user_categories_images = $this->UserCategories_model->getUserLikedCategoryImages($user_data->userId);

            if ($user_categories_images) {
                $response = [
                    'status' => true,
                    'data' => $user_categories_images,
                ];
                $this->response($response, self::HTTP_OK);
            } else {
                $response = [
                    'status' => false,
                    'message' => 'No categories selected by user',
                ];
                $this->response($response, self::HTTP_OK);
            }


        } else {
            $data = [
                'status' => false,
                'message' => 'UnAuthorized'
            ];
            $this->response($data, self::HTTP_UNAUTHORIZED);
        }
    }
}