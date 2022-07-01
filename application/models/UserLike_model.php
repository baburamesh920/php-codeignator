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
class UserLike_model extends CI_Model
{

    /**
     * This function is used to add new Category to system.
     *
     * @param {mixed} $imageInfo
     *
     * @return number $insert_id : This is last inserted id
     */
    public function addUserLiked($userlikedInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_user_liked', $userlikedInfo);

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
    public function getAllUserlikedImages($userId)
    {
        $this->db->select('*');
        $this->db->from('tbl_user_liked');
        $this->db->join('tbl_images', 'tbl_images.imageId = tbl_user_liked.imageId', 'left');
        $this->db->where('tbl_user_liked.userId', $userId);
        $query = $this->db->get();

        return $query->result();
    }

    public function getAllUserlikedImagesByCategory($userId, $categoryId)
    {
        $this->db->select('*');
        $this->db->from('tbl_user_liked');
        $this->db->join('tbl_images', 'tbl_images.imageId = tbl_user_liked.imageId', 'left');
        $this->db->where('tbl_user_liked.userId', $userId);
        $this->db->where('tbl_images.categoryId', $categoryId);
        $query = $this->db->get();

        return $query->result();
    }

    public function getUserliked($userId, $imageId)
    {
        $this->db->select('*');
        $this->db->from('tbl_user_liked');
        $this->db->join('tbl_images', 'tbl_images.imageId = tbl_user_liked.imageId', 'left');
        $this->db->where('tbl_user_liked.userId', $userId);
        $this->db->where('tbl_user_liked.imageId', $imageId);
        $query = $this->db->get();

        return $query->row();
    }

    /**
     * This function is used to update the image information.
     *
     * @param array $imageInfo : This is image updated information
     * @param number $imageId : This is image id
     */
    public function deleteUserliked($userId, $imageId)
    {

        $this->db->where('userId', $userId);
        $this->db->where('imageId', $imageId);
        $this->db->delete('tbl_user_liked');

        return true;
    }


    public function getMostLikedImages($userId = null)
    {
        if ($userId) {
            $this->db->select('tbl_images.*, count(*) as total_likes, if(user_liked.id, 1, 0)   as liked');
            $this->db->join('tbl_images', 'tbl_images.imageId = tbl_user_liked.imageId', 'left');
            $this->db->join('(select * from tbl_user_liked where tbl_user_liked.userId = ' . $userId . ') as user_liked', 'tbl_images.imageId = user_liked.imageId', 'left');
            $this->db->from('tbl_user_liked');
            $this->db->where('tbl_images.isDeleted', 0);
            $this->db->group_by('tbl_user_liked.imageId');
            $query = $this->db->get();
        } else {
            $this->db->select('count(*) as total_likes,tbl_images.*');
            $this->db->join('tbl_images', 'tbl_images.imageId = tbl_user_liked.imageId', 'left');
            $this->db->from('tbl_user_liked');
            $this->db->where('tbl_images.isDeleted', 0);
            $this->db->group_by('tbl_user_liked.imageId');
            $query = $this->db->get();
        }
        $result = $query->result();
        return $result;
    }

    public function getMostLikedImagesByCategory($categoryId, $userId = null)
    {
        if ($userId) {
            $this->db->select('tbl_images.*, count(*) as total_likes, if(user_liked.id, 1, 0)   as liked');
            $this->db->join('tbl_images', 'tbl_images.imageId = tbl_user_liked.imageId', 'left');
            $this->db->join('(select * from tbl_user_liked where tbl_user_liked.userId = ' . $userId . ') as user_liked', 'tbl_images.imageId = user_liked.imageId', 'left');
            $this->db->from('tbl_user_liked');
            $this->db->where('tbl_images.categoryId', $categoryId);
            $this->db->where('tbl_images.isDeleted', 0);
            $this->db->group_by('tbl_user_liked.imageId');
            $query = $this->db->get();
        } else {
            $this->db->select('count(*) as total_likes,tbl_images.*');
            $this->db->join('tbl_images', 'tbl_images.imageId = tbl_user_liked.imageId', 'left');
            $this->db->from('tbl_user_liked');
            $this->db->where('tbl_images.categoryId', $categoryId);
            $this->db->where('tbl_images.isDeleted', 0);
            $this->db->group_by('tbl_user_liked.imageId');
            $query = $this->db->get();
        }
        $result = $query->result();
        return $result;
    }
}
