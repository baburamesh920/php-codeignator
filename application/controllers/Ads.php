<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : Ads (AdsController)
 * Ads Class to control all user related operations.
 *
 * @author : Kishor Mali
 *
 * @version : 1.1
 *
 * @since : 15 November 2016
 */
class Ads extends BaseController
{
    /**
     * This is default constructor of the class.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ads_model');
        $this->isLoggedIn();
    }

    /**
     * This function is used to load the user list.
     */
    public function adsListing()
    {
        if (true == $this->isUser()) {
            $this->loadThis();
        } else {
            $searchText = $this->security->xss_clean($this->input->post('searchText'));
            $data['searchText'] = $searchText;

            $this->load->library('pagination');

            $count = $this->ads_model->adsListingCount($searchText);

            $returns = $this->paginationCompress('adsListing/', $count, 10);

            $data['imagesRecords'] = $this->ads_model->adsListing($searchText, $returns['page'], $returns['segment']);

            $this->global['pageTitle'] = 'InfoCards : Ads Listing';

            $this->loadViews('ads/ads', $this->global, $data, null);
        }
    }

    /**
     * This function is used to add new user to the system.
     */
    public function addNewAds()
    {
        if (true == $this->isUser()) {
            $this->loadThis();
        } else {
            $this->load->library('form_validation');
            $this->load->helper('file');

            $this->form_validation->set_rules('adsTitle', 'Ad Title', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('adsLink', 'Ad Link', 'trim|required');

            if (empty($_FILES['image']['name'])) {
                $this->form_validation->set_rules('image', 'Document', 'required');
            }

            if (false == $this->form_validation->run()) {
                $this->addNew();
            } else {
                if (true == $this->form_validation->run()) {
                    $config['upload_path'] = 'assets/uploads/Ads/';
                    $config['allowed_types'] = 'jpg|png|jpeg';

                    if (!is_dir($config['upload_path'])) {
                        mkdir($config['upload_path'], 0777, TRUE);
                    }

                    $this->load->library('upload', $config);
                    if ($this->upload->do_upload('image')) {
                        $uploadData = $this->upload->data();
                        $uploadedFile = $uploadData['file_name'];

                        $adsTitle = $this->input->post('adsTitle');
                        $adsLink = $this->input->post('adsLink');

                        $adsInfo = [
                            'adsLink' => $adsLink, 'adsTitle' => $adsTitle, 'adsImage' => 'assets/uploads/Ads/' . $uploadedFile, 'createdAt' => date('Y-m-d H:i:s'),
                        ];

                        $this->load->model('ads_model');
                        $result = $this->ads_model->addNewAds($adsInfo);

                        if ($result > 0) {
                            $this->session->set_flashdata('success', 'New Ad created successfully');
                        } else {
                            $this->session->set_flashdata('error', 'Ad creation failed');
                        }
                    } else {
                        $this->session->set_flashdata('error', $this->upload->display_errors());
                        // $data['error_msg'] = $this->upload->display_errors();
                    }
                    redirect('adsListing');
                }
            }
        }
    }

    /**
     * This function is used to load the add new form.
     */
    public function addNew()
    {
        if (true == $this->isUser()) {
            $this->loadThis();
        } else {
            $this->global['pageTitle'] = 'InfoCards : Add New Ads';

            $this->loadViews('ads/add', $this->global, null);
        }
    }

    /**
     * This function is used load user edit information.
     *
     * @param number $adsId : Optional : This is image id
     * @param null|mixed $adsId
     */
    public function editAds($adsId = null)
    {
        if (true == $this->isUser()) {
            $this->loadThis();
        } else {
            $data['adsInfo'] = $this->ads_model->getAdsInfo($adsId);

            $this->global['pageTitle'] = 'InfoCards : Edit Ads';

            $this->loadViews('ads/edit', $this->global, $data, null);
        }
    }

    /**
     * This function is used to edit the user information.
     */
    public function editAdsPost()
    {
        if (true == $this->isUser()) {
            $this->loadThis();
        } else {
            $this->load->library('form_validation');
            $this->load->helper('file');

            $this->form_validation->set_rules('adsTitle', 'Ad Title', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('adsLink', 'Ad Link', 'trim|required');

            if (empty($_FILES['image']['name'])) {
                $this->form_validation->set_rules('image', 'Document', 'required');
            }
            $adsId = $this->input->post('adsId');

            if (false == $this->form_validation->run()) {
                $this->addNew();
            } else {
                if (true == $this->form_validation->run()) {
                    $uploadPath = 'assets/uploads/Ads/';
                    $config['upload_path'] = $uploadPath;
                    $config['allowed_types'] = 'jpg|png|jpeg';

                    $this->load->library('upload', $config);
                    if ($this->upload->do_upload('image')) {
                        $data = (array)$this->ads_model->getAdsInfo($adsId);
                        $old_image_path = $uploadPath . $data['adsImage'];
                        unlink($old_image_path);

                        $uploadData = $this->upload->data();
                        $uploadedFile = $uploadData['file_name'];

                        $adsTitle = $this->input->post('adsTitle');
                        $adsLink = $this->input->post('adsLink');

                        $adsInfo = [
                            'adsLink' => $adsLink, 'adsTitle' => $adsTitle, 'adsImage' => 'assets/uploads/Ads/' .$uploadedFile, 'updatedAt' => date('Y-m-d H:i:s'),
                        ];

                        $this->load->model('ads_model');
                        $result = $this->ads_model->editAds($adsInfo, $adsId);

                        if ($result > 0) {
                            $this->session->set_flashdata('success', 'Ads Updated successfully');
                        } else {
                            $this->session->set_flashdata('error', 'Ads Update failed');
                        }
                    } else {
                        $this->session->set_flashdata('error', $this->upload->display_errors());
                        // $data['error_msg'] = $this->upload->display_errors();
                    }
                } else {
                    $adsTitle = $this->input->post('adsTitle');
                    $adsLink = $this->input->post('adsLink');

                    $adsInfo = [
                        'adsLink' => $adsLink, 'adsTitle' => $adsTitle, 'updatedAt' => date('Y-m-d H:i:s'),
                    ];

                    $result = $this->ads_model->editAds($adsInfo, $adsId);

                    if ($result > 0) {
                        $this->session->set_flashdata('success', 'Ads Updated successfully');
                    } else {
                        $this->session->set_flashdata('error', 'Ads Update failed');
                    }
                }
            }

            redirect('adsListing');
        }
    }

    /**
     * This function is used to delete the user using userId.
     *
     * @return bool $result : TRUE / FALSE
     */
    public function deleteAds()
    {
        if (true == $this->isUser()) {
            echo json_encode(['status' => 'access']);
        } else {
            $adsId = $this->input->post('adsId');
            $adsInfo = ['isDeleted' => 1, 'updatedAt' => date('Y-m-d H:i:s')];

            $result = $this->ads_model->deleteAds($adsId, $adsInfo);

            if ($result > 0) {
                echo json_encode(['status' => true]);
            } else {
                echo json_encode(['status' => false]);
            }
        }
    }
}
