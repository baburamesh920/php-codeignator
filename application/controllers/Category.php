<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH.'/libraries/BaseController.php';

/**
 * Class : Category (CategoryController)
 * Category Class to control all Category related operations.
 *
 * @author
 *
 * @version
 *
 * @since
 */
class Category extends BaseController
{
    /**
     * This is default constructor of the class.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('categories_model');
        $this->isLoggedIn();
    }

    /**
     * This function used to load the first screen of the user.
     */
    public function index()
    {
        $this->global['pageTitle'] = 'InfoCards : Categories';

        $this->loadViews('dashboard', $this->global, null, null);
    }

    /**
     * This function is used to load the user list.
     */
    public function categoryListing()
    {
        if (true == $this->isAdmin()) {
            $this->loadThis();
        } else {
            $searchText = $this->security->xss_clean($this->input->post('searchText'));
            $data['searchText'] = $searchText;

            $this->load->library('pagination');

            $count = $this->categories_model->categoryListingCount($searchText);

            $returns = $this->paginationCompress('categoryListing/', $count, 10);

            $data['categoryRecords'] = $this->categories_model->categoryListing($searchText, $returns['page'], $returns['segment']);

            $this->global['pageTitle'] = 'InfoCards : Category Listing';

            $this->loadViews('category/categories', $this->global, $data, null);
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
            $this->load->model('categories_model');
            $data['categories'] = $this->categories_model->getCategories();

            $this->global['pageTitle'] = 'InfoCards : Add New Category';

            $this->loadViews('category/add', $this->global, $data, null);
        }
    }

    /**
     * This function is used to add new user to the system.
     */
    public function addNewCategory()
    {
        if (true == $this->isUser()) {
            $this->loadThis();
        } else {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('categoryName', 'Category Name', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('parentId', 'Role', 'trim|numeric');
            if (empty($_FILES['image']['name'])) {
                $this->form_validation->set_rules('image', 'Document', 'required');
            }

            if (false == $this->form_validation->run()) {
                $this->addNew();
            } else {
                $config['upload_path'] = 'assets/uploads/categories/';
                $config['allowed_types'] = 'jpg|png|jpeg';

                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0777, TRUE);

                }

                $this->load->library('upload', $config);
                if ($this->upload->do_upload('image')) {
                    $uploadData = $this->upload->data();
                    $uploadedFile = $uploadData['file_name'];
                    $categoryName = ucwords(strtolower($this->security->xss_clean($this->input->post('categoryName'))));
                    $parentId = empty($this->input->post('parentId')) ? null : $this->input->post('parentId');

                    $categoryInfo = ['categoryName' => $categoryName, 'categoryImage' => 'assets/uploads/categories/'.$uploadedFile, 'parentId' => $parentId, 'createdAt' => date('Y-m-d H:i:s')];

                    $this->load->model('categories_model');
                    $result = $this->categories_model->addNewCategory($categoryInfo);

                    if ($result > 0) {
                        $this->session->set_flashdata('success', 'New Category created successfully');
                    } else {
                        $this->session->set_flashdata('error', 'Category creation failed');
                    }
                } else {
                    $this->session->set_flashdata('error', $this->upload->display_errors());
                    $this->addNew();
                    die;
                }

                redirect('categoryListing');
            }
        }
    }

    /**
     * This function is used load Category edit information.
     *
     * @param number $categoryId :  This is category id
     */
    public function editCategory($categoryId)
    {
        if (true == $this->isUser()) {
            $this->loadThis();
        } else {
            // $this->load->model('categories_model');

            $data['categories'] = $this->categories_model->getCategories();
            $data['categoryInfo'] = $this->categories_model->getCategoryInfo($categoryId);

            $this->global['pageTitle'] = 'InfoCards : Edit Category';

            $this->loadViews('category/edit', $this->global, $data, null);
        }
    }

    /**
     * This function is used to edit the user information.
     */
    public function editCategoryPost()
    {
        if (true == $this->isUser()) {
            $this->loadThis();
        } else {
            $this->load->library('form_validation');

            $categoryId = $this->input->post('categoryId');

            $this->form_validation->set_rules('categoryName', 'Full Name', 'trim|required|max_length[128]');
            $this->form_validation->set_rules('parentId', 'Role', 'trim|numeric');

            if (false == $this->form_validation->run()) {
                $this->editCategory($categoryId);
            } else {
                if (true == $this->form_validation->run()) {
                    $uploadPath = 'assets/uploads/categories/';
                    $config['upload_path'] = $uploadPath;
                    $config['allowed_types'] = 'jpg|png|jpeg';

                    $this->load->library('upload', $config);
                    if ($this->upload->do_upload('image')) {
                        $data = (array) $this->categories_model->getCategoryInfo($categoryId);
                        //$old_image_path = $data['imagePath'];
                        $old_image_path = $uploadPath.$data['categoryImage'];

                        unlink($old_image_path);

                        $uploadData = $this->upload->data();
                        $uploadedFile = $uploadData['file_name'];
                        $categoryName = ucwords(strtolower($this->security->xss_clean($this->input->post('categoryName'))));
                        $parentId = empty($this->input->post('parentId')) ? null : $this->input->post('parentId');

                        $categoryInfo = [];

                        $categoryInfo = ['categoryName' => $categoryName, 'categoryImage' =>'assets/uploads/categories/'. $uploadedFile, 'parentId' => $parentId,  'updatedAt' => date('Y-m-d H:i:s')];

                        $result = $this->categories_model->editCategory($categoryInfo, $categoryId);

                        if (true == $result) {
                            $this->session->set_flashdata('success', 'Category updated successfully');
                        } else {
                            $this->session->set_flashdata('error', 'Category updation failed');
                        }
                    } else {
                        $this->session->set_flashdata('error', $this->upload->display_errors());
                        // $data['error_msg'] = $this->upload->display_errors();
                    }
                } else {
                    $categoryName = ucwords(strtolower($this->security->xss_clean($this->input->post('categoryName'))));
                    $parentId = empty($this->input->post('parentId')) ? null : $this->input->post('parentId');

                    $categoryInfo = [
                        'categoryName' => $categoryName, 'parentId' => $parentId, 'updatedAt' => date('Y-m-d H:i:s'),
                    ];

                    $result = $this->images_model->editImage($categoryInfo, $categoryId);

                    if ($result > 0) {
                        $this->session->set_flashdata('success', 'New Image created successfully');
                    } else {
                        $this->session->set_flashdata('error', 'Image creation failed');
                    }
                }

                redirect('categoryListing');
            }
        }
    }

    /**
     * This function is used to delete the user using userId.
     *
     * @return bool $result : TRUE / FALSE
     */
    public function deleteCategory()
    {
        if (true == $this->isUser()) {
            $this->loadThis();
        } else {
            $categoryId = $this->input->post('categoryId');
            $categoryInfo = ['isDeleted' => 1, 'updatedAt' => date('Y-m-d H:i:s')];

            $result = $this->categories_model->deleteCategory($categoryId, $categoryInfo);

            if ($result > 0) {
                echo json_encode(['status' => true]);
            } else {
                echo json_encode(['status' => false]);
            }
        }
    }
}
