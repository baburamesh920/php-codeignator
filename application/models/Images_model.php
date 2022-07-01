<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class : User_model (Categories Model)
 * User model class to get to handle user related data.
 *
 * @author
 *
 * @version
 *
 * @since
 */
class Images_model extends CI_Model
{

    /**
     * This function is used to get the Images listing count.
     *
     * @param string $searchText : This is optional search text
     *
     * @return number $count : This is row count
     */

    public function imagesListingCount($searchText = '')
    {
        $this->db->select('*');
        $this->db->from('tbl_images');
        $this->db->join('tbl_categories as category', 'tbl_images.categoryId = category.categoryId', 'left');
        if (!empty($searchText)) {
            $likeCriteria = "(tbl_images.imageName  LIKE '%" . $searchText . "%'
                                 OR  category.categoryName  LIKE '%" . $searchText . "%'
                                 OR JSON_SEARCH(tbl_images.imageTags, 'one','%" . $searchText . "%') is not null)";
            $this->db->where($likeCriteria);
        }
        $this->db->where('tbl_images.isDeleted', 0);
        $this->db->where('category.isDeleted', 0);

        $query = $this->db->get();

        return $query->num_rows();
    }

    /**
     * This function is used to get the Images listing count.
     *
     * @param string $searchText : This is optional search text
     * @param number $page : This is pagination offset
     * @param number $segment : This is pagination limit
     *
     * @return array $result : This is result
     */
    public function imagesListing($searchText = '', $page, $segment)
    {
        $this->db->select('*');
        $this->db->from('tbl_images');
        $this->db->join('tbl_categories as category', 'tbl_images.categoryId = category.categoryId', 'left');
        if (!empty($searchText)) {
            $likeCriteria = "(tbl_images.imageName  LIKE '%" . $searchText . "%'
                                 OR  category.categoryName  LIKE '%" . $searchText . "%' 
                                 OR JSON_SEARCH(tbl_images.imageTags, 'one','%" . $searchText . "%') is not null)";
            $this->db->where($likeCriteria);
        }
        $this->db->where('tbl_images.isDeleted', 0);
        $this->db->where('category.isDeleted', 0);
        $this->db->order_by('tbl_images.imageName', 'ASC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * This function is used to add new Category to system.
     *
     * @param {mixed} $imageInfo
     *
     * @return number $insert_id : This is last inserted id
     */
    public function addNewImage($imageInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_images', $imageInfo);

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
    public function getImageInfo($imageId)
    {
        $this->db->select('*');
        $this->db->from('tbl_images');
        $this->db->join('tbl_categories as category', 'tbl_images.categoryId = category.categoryId', 'left');
        $this->db->where('tbl_images.imageId', $imageId);
        $this->db->where('tbl_images.isDeleted', 0);
        $this->db->where('category.isDeleted', 0);
        $query = $this->db->get();

        return $query->row();
    }

    /**
     * This function is used to update the image information.
     *
     * @param array $imageInfo : This is image updated information
     * @param number $imageId : This is image id
     */
    public function editImage($imageInfo, $imageId)
    {
        $this->db->where('imageId', $imageId);
        $this->db->update('tbl_images', $imageInfo);

        return true;
    }


    /**
     * This function is used to delete the image information.
     *
     * @param number $imageId : This is image id
     * @param mixed $imageInfo
     *
     * @return bool $result : TRUE / FALSE
     */
    public function deleteImage($imageId, $imageInfo)
    {
        $this->db->where('imageId', $imageId);
        $this->db->update('tbl_images', $imageInfo);

        return $this->db->affected_rows();
    }


    /**
     * This function used to get user information by id with role.
     *
     * @param number $categoryId : This is user id
     *
     * @return aray $result : This is user information
     */
    public function getImagesBYCategory($categoryId, $userId = null)
    {
        if ($userId) {
            $this->db->select('tbl_images.*, category.*,if(user_liked.id, 1, 0)  as liked');
            $this->db->from('tbl_images');
            $this->db->join('tbl_categories as category', 'tbl_images.categoryId = category.categoryId', 'left');
            $this->db->join('(select * from tbl_user_liked where tbl_user_liked.userId = ' . $userId . ') as user_liked', 'tbl_images.imageId = user_liked.imageId', 'left');
            $this->db->where('tbl_images.categoryId', $categoryId);
            $this->db->where('tbl_images.isDeleted', 0);
            $this->db->where('category.isDeleted', 0);
            $query = $this->db->get();

        } else {
            $this->db->select('*');
            $this->db->from('tbl_images');
            $this->db->join('tbl_categories as category', 'tbl_images.categoryId = category.categoryId', 'left');
            $this->db->where('tbl_images.categoryId', $categoryId);
            $this->db->where('tbl_images.isDeleted', 0);
            $this->db->where('category.isDeleted', 0);
            $query = $this->db->get();
        }

        return $query->result();
    }

    /**
     * This function used to get user information by id with role.
     *
     * @param number $categoryId : This is user id
     *
     * @return aray $result : This is user information
     */
    public function getAllImages($userId = null, $searchText = null)
    {
        if ($userId) {
            $this->db->select('tbl_images.*, category.*,if(user_liked.id, 1, 0)  as liked');
            $this->db->from('tbl_images');
            $this->db->join('tbl_categories as category', 'tbl_images.categoryId = category.categoryId', 'left');
            $this->db->join('(select * from tbl_user_liked where tbl_user_liked.userId = ' . $userId . ') as user_liked', 'tbl_images.imageId = user_liked.imageId', 'left');
            if (!empty($searchText)) {
//                $likeCriteria = "(tbl_images.imageName  LIKE '%" . $searchText . "%'
//                                 OR  category.categoryName  LIKE '%" . $searchText . "%'
//                                 OR JSON_SEARCH(tbl_images.imageTags, 'one','%" . $searchText . "%') is not null)";
                $likeCriteria = "(JSON_SEARCH(tbl_images.imageTags, 'one','%" . $searchText . "%') is not null)";
                $this->db->where($likeCriteria);
            }
            $this->db->where('tbl_images.isDeleted', 0);
            $this->db->where('category.isDeleted', 0);
            $query = $this->db->get();
        } else {
            $this->db->select('*');
            $this->db->from('tbl_images');
            $this->db->join('tbl_categories as category', 'tbl_images.categoryId = category.categoryId', 'left');
            if (!empty($searchText)) {
//                $likeCriteria = "(tbl_images.imageName  LIKE '%" . $searchText . "%'
//                                 OR  category.categoryName  LIKE '%" . $searchText . "%'
//                                 OR JSON_SEARCH(tbl_images.imageTags, 'one','%" . $searchText . "%') is not null)";
                $likeCriteria = "(JSON_SEARCH(tbl_images.imageTags, 'one','%" . $searchText . "%') is not null)";
                $this->db->where($likeCriteria);
            }
            $this->db->where('tbl_images.isDeleted', 0);
            $this->db->where('category.isDeleted', 0);
            $query = $this->db->get();
        }

        return $query->result();
    }

    public function getRecentelyAddedImages($userId = null)
    {

        if ($userId) {
            $this->db->select('tbl_images.*, category.*,if(user_liked.id, 1, 0)  as liked');
            $this->db->from('tbl_images');
            $this->db->join('tbl_categories as category', 'tbl_images.categoryId = category.categoryId', 'left');
            $this->db->join('(select * from tbl_user_liked where tbl_user_liked.userId = ' . $userId . ') as user_liked', 'tbl_images.imageId = user_liked.imageId', 'left');
            $this->db->where('tbl_images.isDeleted', 0);
            $this->db->where('category.isDeleted', 0);
            $this->db->order_by('tbl_images.createdAt', 'desc');
            $query = $this->db->get();
        } else {

            $this->db->select('*');
            $this->db->from('tbl_images');
            $this->db->join('tbl_categories as category', 'tbl_images.categoryId = category.categoryId', 'left');
            $this->db->where('tbl_images.isDeleted', 0);
            $this->db->where('category.isDeleted', 0);
            $this->db->order_by('tbl_images.createdAt', 'desc');
            $query = $this->db->get();
        }

        return $query->result();
    }

    public function getRecentelyAddedImagesByCategory($categoryId, $userId = null)
    {
        if ($userId) {
            $this->db->select('tbl_images.*, category.*,if(user_liked.id, 1, 0)  as liked');
            $this->db->from('tbl_images');
            $this->db->join('tbl_categories as category', 'tbl_images.categoryId = category.categoryId', 'left');
            $this->db->join('(select * from tbl_user_liked where tbl_user_liked.userId = ' . $userId . ') as user_liked', 'tbl_images.imageId = user_liked.imageId', 'left');
            $this->db->where('tbl_images.isDeleted', 0);
            $this->db->where('category.isDeleted', 0);
            $this->db->where('tbl_images.categoryId', $categoryId);
            $this->db->order_by('tbl_images.createdAt', 'desc');
            $query = $this->db->get();
        } else {

            $this->db->select('*');
            $this->db->from('tbl_images');
            $this->db->join('tbl_categories as category', 'tbl_images.categoryId = category.categoryId', 'left');
            $this->db->where('tbl_images.isDeleted', 0);
            $this->db->where('tbl_images.categoryId', $categoryId);
            $this->db->where('category.isDeleted', 0);
            $this->db->order_by('tbl_images.createdAt', 'desc');
            $query = $this->db->get();
        }
        return $query->result();
    }
}
