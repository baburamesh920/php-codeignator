<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Category extends RestController
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('categories_model');
    }

    public function categories_get()
    {
        $categories = $this->categories_model->getAllCategories();

        if (!empty($categories)) {
            // Set the response and exit
            $this->response([
                'status' => true,
                'data' => $categories],
                self::HTTP_OK);
        } else {
            // Set the response and exit
            $this->response([
                'status' => false,
                'message' => 'No Categories were found',
            ], self::HTTP_NOT_FOUND);
        }
    }
}
