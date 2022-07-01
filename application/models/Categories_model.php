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
class Categories_model extends CI_Model
{

    public function getCategories()
    {
        $this->db->select('*');
        $this->db->from('tbl_categories');
        $this->db->where('parentId is NULL', null, false);
        $this->db->where('isDeleted', 0);

        $parent = $this->db->get();

        $categories = $parent->result();
        $i = 0;
        foreach ($categories as $p_cat) {
            $categories[$i]->sub = $this->subCategories($p_cat->categoryId);
            ++$i;
        }

        return $categories;
    }

    public function getAllCategories()
    {
        $this->db->select('*');
        $this->db->from('tbl_categories');
        $this->db->where('isDeleted', 0);

        $parent = $this->db->get();

        return $parent->result();
    }

    public function subCategories($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_categories');
        $this->db->where('parentId', $id);
        $this->db->where('isDeleted', 0);

        $child = $this->db->get();
        $categories = $child->result();
        $i = 0;
        foreach ($categories as $p_cat) {
            $categories[$i]->sub = $this->subCategories($p_cat->categoryId);
            ++$i;
        }

        return $categories;
    }

    public function categoryListingCount($searchText = '')
    {
        $this->db->select('category.categoryId,category.categoryName,parent.categoryId as parentId, parent.categoryName as parentName,category.createdAt');
        $this->db->from('tbl_categories as category');
        $this->db->join('tbl_categories as parent', 'parent.categoryId = category.parentId', 'left');
        if (!empty($searchText)) {
            $likeCriteria = "(category.categoryName  LIKE '%".$searchText."%'
                                OR  parent.categoryName  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $this->db->where('category.isDeleted', 0);

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function categoryListing($searchText = '', $page, $segment)
    {
        $this->db->select('category.categoryId,category.categoryName,parent.categoryId as parentId, parent.categoryName as parentName,category.createdAt');
        $this->db->from('tbl_categories as category');
        $this->db->join('tbl_categories as parent', 'parent.categoryId = category.parentId', 'left');
        if (!empty($searchText)) {
            $likeCriteria = "(category.categoryName  LIKE '%".$searchText."%'
                                 OR  parent.categoryName  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $this->db->where('category.isDeleted', 0);
        $this->db->order_by('category.categoryName', 'ASC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * This function is used to add new Category to system.
     *
     * @param {mixed} $categoryInfo
     *
     * @return number $insert_id : This is last inserted id
     */
    public function addNewCategory($categoryInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_categories', $categoryInfo);

        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();

        return $insert_id;
    }

    /**
     * This function used to get Category information by id.
     *
     * @param number $categoryId : This is Category id
     *
     * @return array $result : This is Category information
     */
    public function getCategoryInfo($categoryId)
    {
        $this->db->select('category.categoryId,category.categoryName,category.categoryImage,parent.categoryId as parentId, parent.categoryName as parentName,category.createdAt');
        $this->db->from('tbl_categories as category');
        $this->db->join('tbl_categories as parent', 'parent.categoryId = category.parentId', 'left');
        $this->db->where('category.categoryId', $categoryId);
        $this->db->where('category.isDeleted', 0);
        $query = $this->db->get();

        return $query->row();
    }

    /**
     * This function is used to update the category information.
     *
     * @param array  $categoryInfo : This is users updated information
     * @param number $categoryId   : This is user id
     */
    public function editCategory($categoryInfo, $categoryId)
    {
        $this->db->where('categoryId', $categoryId);
        $this->db->update('tbl_categories', $categoryInfo);

        return true;
    }

    /**
     * This function is used to delete the category information.
     *
     * @param number $categoryId   : This is user id
     * @param mixed  $categoryInfo
     *
     * @return bool $result : TRUE / FALSE
     */
    public function deleteCategory($categoryId, $categoryInfo)
    {
        $this->db->where('categoryId', $categoryId);
        $this->db->update('tbl_categories', $categoryInfo);

        return $this->db->affected_rows();
    }
}
