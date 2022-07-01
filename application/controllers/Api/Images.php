<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Images extends RestController
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('images_model');
        $this->load->model('UserLike_model');
    }

    public function allImages_get()
    {
        $headers = $this->input->request_headers();
        if (array_key_exists('Authorization', $headers)) {
            $user_data = checkJwtToken($headers);
            $images = $this->images_model->getAllImages($user_data->userId);
        } else {
            $images = $this->images_model->getAllImages();
        }
        if (!empty($images)) {
            // Set the response and exit
            $this->response([
                'status' => true,
                'data' => $images
            ], self::HTTP_OK);
        } else {
            // Set the response and exit
            $this->response([
                'status' => false,
                'message' => 'No Images were found',
            ], self::HTTP_NOT_FOUND);
        }
    }

    public function getImagesBYSearch_post()
    {
        $searchText = $this->input->post('searchText') ? $this->input->post('searchText') : '';
        $headers = $this->input->request_headers();
        if (array_key_exists('Authorization', $headers)) {
            $user_data = checkJwtToken($headers);
            $images = $this->images_model->getAllImages($user_data->userId, $searchText);
        } else {
            $images = $this->images_model->getAllImages(null, $searchText);
        }
        if (!empty($images)) {
            // Set the response and exit
            $this->response([
                'status' => true,
                'data' => $images
            ], self::HTTP_OK);
        } else {
            // Set the response and exit
            $this->response([
                'status' => false,
                'message' => 'No Images were found',
            ], self::HTTP_NOT_FOUND);
        }
    }

    public function imagesByCategory_get($categoryId)
    {
        $headers = $this->input->request_headers();
        if (array_key_exists('Authorization', $headers)) {
            $user_data = checkJwtToken($headers);
            $images = $this->images_model->getImagesBYCategory($categoryId, $user_data->userId);
        } else {

            $images = $this->images_model->getImagesBYCategory($categoryId);
        }

//        echo "<pre>";
//        print_r($this->db->last_query());
//        die;
        if (!empty($images)) {
            // Set the response and exit
            $this->response([
                'status' => true,
                'data' => $images
            ], self::HTTP_OK);
        } else {
            // Set the response and exit
            $this->response([
                'status' => false,
                'message' => 'No Images were found',
            ], self::HTTP_NOT_FOUND);
        }
    }

    public function imageLike_post()
    {
        $headers = $this->input->request_headers();
        $user_data = checkJwtToken($headers);
        if ($user_data) {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('imageId', 'Image Id', 'required');

            if (false == $this->form_validation->run()) {
                $errors = str_replace("\n", "", strip_tags(validation_errors()));
                $this->response([
                    'status' => false,
                    'message' => $errors,
                ], self::HTTP_BAD_REQUEST);
            } else {
                $userId = $user_data->userId;
                $imageId = $this->input->post('imageId');

                $userLikeInfo = ['userId' => $userId, 'imageId' => $imageId];

                $result = $this->UserLike_model->addUserLiked($userLikeInfo);

                if ($result) {
                    $this->response([
                        'status' => true,
                        'message' => 'Image liked successfully.'
                    ], self::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => 'Image liked  failed.'
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

    public function imagedisLike_post()
    {
        $headers = $this->input->request_headers();
        $user_data = checkJwtToken($headers);
        if ($user_data) {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('imageId', 'Image Id', 'required');

            if (false == $this->form_validation->run()) {
                $errors = str_replace("\n", "", strip_tags(validation_errors()));
                $this->response([
                    'status' => false,
                    'message' => $errors,
                ], self::HTTP_BAD_REQUEST);
            } else {
                $userId = $user_data->userId;
                $imageId = $this->input->post('imageId');

                $userLikeInfo = ['userId' => $userId, 'imageId' => $imageId];

                $result = $this->UserLike_model->deleteUserliked($userId, $imageId);

                if ($result) {
                    $this->response([
                        'status' => true,
                        'message' => 'Image disliked successfully.'
                    ], self::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => 'Image disliked  failed.'
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

    public function userLikedImages_get()
    {
        $headers = $this->input->request_headers();
        $user_data = checkJwtToken($headers);
        if ($user_data) {
            $images = $this->UserLike_model->getAllUserlikedImages($user_data->userId);

            if ($images) {
                $this->response([
                    'status' => true,
                    'data' => $images
                ], self::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No liked images'
                ], self::HTTP_NOT_FOUND);
            }

        } else {
            $data = [
                'status' => false,
                'message' => 'UnAuthorized'
            ];
            $this->response($data, self::HTTP_UNAUTHORIZED);
        }
    }

    public function userLikedImagesByCategory_get($categoryId)
    {
        $headers = $this->input->request_headers();
        $user_data = checkJwtToken($headers);
        if ($user_data) {

            $images = $this->UserLike_model->getAllUserlikedImagesByCategory($user_data->userId, $categoryId);

            if ($images) {
                $this->response([
                    'status' => true,
                    'data' => $images
                ], self::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'No liked images in this category'
                ], self::HTTP_NOT_FOUND);
            }

        } else {
            $data = [
                'status' => false,
                'message' => 'UnAuthorized'
            ];
            $this->response($data, self::HTTP_UNAUTHORIZED);
        }
    }


    public function getMostUserLikedImages_get()
    {
        $headers = $this->input->request_headers();
        if (array_key_exists('Authorization', $headers)) {
            $user_data = checkJwtToken($headers);
            $images = $this->UserLike_model->getMostLikedImages($user_data->userId);
        } else {
            $images = $this->UserLike_model->getMostLikedImages();
        }

        if ($images) {
            $this->response([
                'status' => true,
                'data' => $images
            ], self::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No liked images in this category'
            ], self::HTTP_NOT_FOUND);
        }
    }

    public function getMostUserLikedImagesByCategory_get($categoryId)
    {
        $headers = $this->input->request_headers();
        if (array_key_exists('Authorization', $headers)) {
            $user_data = checkJwtToken($headers);
            $images = $this->UserLike_model->getMostLikedImagesByCategory($categoryId, $user_data->userId);
        } else {
            $images = $this->UserLike_model->getMostLikedImagesByCategory($categoryId);
        }

        if ($images) {
            $this->response([
                'status' => true,
                'data' => $images
            ], self::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No liked images in this category'
            ], self::HTTP_NOT_FOUND);
        }
    }

    public function getRecentelyAddedImages_get()
    {
        $headers = $this->input->request_headers();
        if (array_key_exists('Authorization', $headers)) {
            $user_data = checkJwtToken($headers);
            $images = $this->images_model->getRecentelyAddedImages($user_data->userId);
        } else {
            $images = $this->images_model->getRecentelyAddedImages();
        }

        if ($images) {
            $this->response([
                'status' => true,
                'data' => $images
            ], self::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No images in this category'
            ], self::HTTP_NOT_FOUND);
        }
    }

    public function getRecentelyAddedImagesByCategory_get($categoryId)
    {
        $headers = $this->input->request_headers();
        if (array_key_exists('Authorization', $headers)) {
            $user_data = checkJwtToken($headers);
            $images = $this->images_model->getRecentelyAddedImagesByCategory($categoryId, $user_data->userId);
        } else {
            $images = $this->images_model->getRecentelyAddedImagesByCategory($categoryId);
        }

        if ($images) {
            $this->response([
                'status' => true,
                'data' => $images
            ], self::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No images in this category'
            ], self::HTTP_NOT_FOUND);
        }
    }
}
