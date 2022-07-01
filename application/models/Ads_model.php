<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class : Ads_model (Ads Model)
 * Ads model class to get to handle user related data.
 *
 * @author
 *
 * @version
 *
 * @since
 */
class Ads_model extends CI_Model
{

    public function getAllAds()
    {
        $this->db->select('*');
        $this->db->from('tbl_ads');
        $this->db->where('isDeleted', 0);

        $parent = $this->db->get();

        return $parent->result();
    }
    /**
     * This function is used to get the Images listing count.
     *
     * @param string $searchText : This is optional search text
     *
     * @return number $count : This is row count
     */
    public function adsListingCount($searchText = '')
    {
        $this->db->select('*');
        $this->db->from('tbl_ads');
        if (!empty($searchText)) {
            $likeCriteria = "(tbl_ads.adsTitle  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $this->db->where('tbl_ads.isDeleted', 0);

        $query = $this->db->get();

        return $query->num_rows();
    }

    /**
     * This function is used to get the Images listing count.
     *
     * @param string $searchText : This is optional search text
     * @param number $page       : This is pagination offset
     * @param number $segment    : This is pagination limit
     *
     * @return array $result : This is result
     */
    public function adsListing($searchText = '', $page, $segment)
    {
        $this->db->select('*');
        $this->db->from('tbl_ads');
        if (!empty($searchText)) {
            $likeCriteria = "(tbl_ads.adsTitle  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $this->db->where('tbl_ads.isDeleted', 0);

        // $this->db->order_by('tbl_images.imageName', 'ASC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * This function is used to add new Category to system.
     *
     * @param {mixed} $imageInfo
     * @param mixed   $adsInfo
     *
     * @return number $insert_id : This is last inserted id
     */
    public function addNewAds($adsInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_ads', $adsInfo);

        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();

        return $insert_id;
    }

    /**
     * This function used to get image information by id.
     *
     * @param number $imageId : This is image id
     * @param mixed  $adsId
     *
     * @return array $result : This is image information
     */
    public function getAdsInfo($adsId)
    {
        $this->db->select('*');
        $this->db->from('tbl_ads');
        $this->db->where('tbl_ads.adsId', $adsId);
        $this->db->where('tbl_ads.isDeleted', 0);
        $query = $this->db->get();

        return $query->row();
    }

    /**
     * This function is used to update the image information.
     *
     * @param array  $imageInfo : This is image updated information
     * @param number $imageId   : This is image id
     * @param mixed  $adsInfo
     * @param mixed  $adsId
     */
    public function editAds($adsInfo, $adsId)
    {
        $this->db->where('adsId', $adsId);
        $this->db->update('tbl_ads', $adsInfo);

        return true;
    }

    /**
     * This function is used to delete the image information.
     *
     * @param number $imageId   : This is image id
     * @param mixed  $imageInfo
     * @param mixed  $adsId
     * @param mixed  $adsInfo
     *
     * @return bool $result : TRUE / FALSE
     */
    public function deleteAds($adsId, $adsInfo)
    {
        $this->db->where('adsId', $adsId);
        $this->db->update('tbl_ads', $adsInfo);

        return $this->db->affected_rows();
    }
}
