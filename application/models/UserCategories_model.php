<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class : User_model (UserCategories Model)
 * UserCategories model class to get to handle user related data.
 *
 * @author
 *
 * @version
 *
 * @since
 */
class UserCategories_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

        /* Load Models - Model_1 */
        $this->load->model('Images_model');
    }

    /**
     * This function is used to add new Category to system.
     *
     * @param {mixed} $imageInfo
     *
     * @return number $insert_id : This is last inserted id
     */
    public function addUsercategory($usercategoryInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_user_categories', $usercategoryInfo);

        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();

        return $insert_id;
    }


    /**
     * This function used to get image information by id.
     *
     * @param number $imageId : This is image id
     *
     * @return array $result : This is image information
     */
    public function getUserCategoriesInfo($userId)
    {
        $this->db->select('*');
        $this->db->from('tbl_user_categories');
        $this->db->where('tbl_user_categories.userId', $userId);
        $query = $this->db->get();

        $result = $query->row();

        $userLikedCategories = json_decode($result->categoriesId);
        return $userLikedCategories;
    }

    public function getUserCategories($userId)
    {
        $this->db->select('*');
        $this->db->from('tbl_user_categories');
        $this->db->where('tbl_user_categories.userId', $userId);
        $query = $this->db->get();

        $result = $query->row();


        return $result;
    }

    /**
     * This function is used to update the image information.
     *
     * @param array $imageInfo : This is image updated information
     * @param number $imageId : This is image id
     */
    public function editUserCategories($usercategoryInfo, $id)
    {

        $this->db->where('id', $id);
        $this->db->update('tbl_user_categories', $usercategoryInfo);

        return true;
    }

    public function getUsersByCategory($categoryId){

        $this->db->select('userId');
        $this->db->from('tbl_user_categories');
        $this->db->where("JSON_CONTAINS(tbl_user_categories.categoriesId,'\"".$categoryId."\"','$') >", 0);
        $query = $this->db->get();

        $result = $query->result();
        return $result;
    }

    public function getUserLikedCategoryImages($userId)
    {

        $userLikedCategories = $this->getUserCategoriesInfo($userId);
        $LikedCategories = implode(",", $userLikedCategories);
        $this->db->select('*');
        $this->db->from('tbl_images');
        $this->db->where('tbl_images.categoryId IN (' . $LikedCategories . ')', null, false);
        $query = $this->db->get();

        $result = $query->result();

        return $result;
    }
}
