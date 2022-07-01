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
class DevicesList_model extends CI_Model
{

    public function getAllDevices()
    {
        $this->db->select('*');
        $this->db->from('tbl_devices_list');
        $this->db->where('isDeleted', 0);

        $parent = $this->db->get();

        return $parent->result();
    }

    /**
     * This function is used to add new Category to system.
     *
     * @param {mixed} $categoryInfo
     *
     * @return number $insert_id : This is last inserted id
     */
    public function addNewDevice($deviceInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_devices_list', $deviceInfo);

        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();

        return true;
    }

    /**
     * This function used to get Category information by id.
     *
     * @param number $deviceId : This is Device id
     *
     * @return array $result : This is Category information
     */
    public function getDeviceInfo($deviceId)
    {
        $this->db->select('*');
        $this->db->from('tbl_devices_list as devices');
        $this->db->where('devices.deviceId', $deviceId);
//        $this->db->where('devices.isDeleted', 0);
        $query = $this->db->get();

        return $query->row();
    }

    /**
     * This function is used to update the category information.
     *
     * @param array $deviceListInfo : This is users updated information
     * @param number $deviceId : This is user id
     */
    public function editDevice($deviceListInfo, $deviceId)
    {
        $this->db->where('deviceId', $deviceId);
        $this->db->update('tbl_devices_list', $deviceListInfo);

        return true;
    }

    /**
     * This function is used to delete the category information.
     *
     * @param number $deviceId : This is user id
     * @param mixed $deviceListInfo
     *
     * @return bool $result : TRUE / FALSE
     */
    public function deleteDevice($deviceId, $deviceListInfo)
    {
        $this->db->where('deviceId', $deviceId);
        $this->db->update('tbl_devices_list', $deviceListInfo);

        return $this->db->affected_rows();
    }
}
