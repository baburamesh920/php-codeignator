<?php

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Ads extends RestController
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Ads_model');
    }

    public function allAds_get()
    {
        $ads = $this->Ads_model->getAllAds();

//        echo "pre>";
//        print_r($this->db->last_query($ads));
//        die;

        if (!empty($ads)) {
            // Set the response and exit
            $this->response([
                'status' => true,
                'data' => $ads],
                self::HTTP_OK);
        } else {
            // Set the response and exit
            $this->response([
                'status' => false,
                'message' => 'No Ads were found',
            ], self::HTTP_NOT_FOUND);
        }
    }
}