<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class : User_model (User Model)
 * User model class to get to handle user related data.
 *
 * @author
 *
 * @version
 *
 * @since
 */
class User_model extends CI_Model
{
    /**
     * This function is used to get the user listing count.
     *
     * @param string $searchText : This is optional search text
     *
     * @return number $count : This is row count
     */
    public function userListingCount($searchText = '')
    {
        $this->db->select('BaseTbl.userId, BaseTbl.email, BaseTbl.name, BaseTbl.mobile, BaseTbl.createdAt, Role.role');
        $this->db->from('tbl_users as BaseTbl');
        $this->db->join('tbl_roles as Role', 'Role.roleId = BaseTbl.roleId', 'left');
        if (!empty($searchText)) {
            $likeCriteria = "(BaseTbl.email  LIKE '%".$searchText."%'
                            OR  BaseTbl.name  LIKE '%".$searchText."%'
                            OR  BaseTbl.mobile  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $this->db->where('BaseTbl.isDeleted', 0);
        $this->db->where('BaseTbl.roleId !=', 1);
        $query = $this->db->get();

        return $query->num_rows();
    }

    /**
     * This function is used to get the user listing count.
     *
     * @param string $searchText : This is optional search text
     * @param number $page       : This is pagination offset
     * @param number $segment    : This is pagination limit
     *
     * @return array $result : This is result
     */
    public function userListing($searchText = '', $page, $segment)
    {
        $this->db->select('BaseTbl.userId, BaseTbl.email, BaseTbl.name, BaseTbl.mobile, BaseTbl.createdAt, Role.role');
        $this->db->from('tbl_users as BaseTbl');
        $this->db->join('tbl_roles as Role', 'Role.roleId = BaseTbl.roleId', 'left');
        if (!empty($searchText)) {
            $likeCriteria = "(BaseTbl.email  LIKE '%".$searchText."%'
                            OR  BaseTbl.name  LIKE '%".$searchText."%'
                            OR  BaseTbl.mobile  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $this->db->where('BaseTbl.isDeleted', 0);
        $this->db->where('BaseTbl.roleId !=', 1);
        $this->db->order_by('BaseTbl.userId', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * this function is used to get all users
     *
     * @return array $result: this is result of the query
     */
    public function allUsers()
    {
        $this->db->select('BaseTbl.userId, BaseTbl.email, BaseTbl.name, BaseTbl.mobile,BaseTbl.firebaseId');
        $this->db->from('tbl_users as BaseTbl');
        $this->db->where('BaseTbl.isDeleted', 0);
        $this->db->where('BaseTbl.roleId ', 3);
        $this->db->order_by('BaseTbl.userId', 'DESC');
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * This function is used to get the user roles information.
     *
     * @return array $result : This is result of the query
     */
    public function getUserRoles()
    {
        $this->db->select('roleId, role');
        $this->db->from('tbl_roles');
        $this->db->where('roleId !=', 1);
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * This function is used to check whether email id is already exist or not.
     *
     * @param {string} $email  : This is email id
     * @param {number} $userId : This is user id
     *
     * @return {mixed} $result : This is searched result
     */
    public function checkEmailExists($email, $userId = 0)
    {
        $this->db->select('email');
        $this->db->from('tbl_users');
        $this->db->where('email', $email);
        $this->db->where('isDeleted', 0);
        if (0 != $userId) {
            $this->db->where('userId !=', $userId);
        }
        $query = $this->db->get();

        return $query->result();
    }


    /**
     * This function is used to check whether email id is already exist or not.
     *
     * @param {string} $mobile  : This is mobile number
     * @param {number} $userId : This is user id
     *
     * @return {mixed} $result : This is searched result
     */
    public function checkMobileExists($mobile, $userId = 0)
    {
        $this->db->select('mobile');
        $this->db->from('tbl_users');
        $this->db->where('mobile', $mobile);
        $this->db->where('isDeleted', 0);
        if (0 != $userId) {
            $this->db->where('userId !=', $userId);
        }
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * This function is used to add new user to system.
     *
     * @param mixed $userInfo
     *
     * @return number $insert_id : This is last inserted id
     */
    public function addNewUser($userInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_users', $userInfo);

        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();

        return $insert_id;
    }

    /**
     * This function used to get user information by id.
     *
     * @param number $userId : This is user id
     *
     * @return array $result : This is user information
     */
    public function getUserInfo($userId)
    {
        $this->db->select('userId, name, email, mobile, roleId, firebaseId');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', 0);
        $this->db->where('roleId !=', 1);
        $this->db->where('userId', $userId);
        $query = $this->db->get();

        return $query->row();
    }

    /**
     * This function is used to update the user information.
     *
     * @param array  $userInfo : This is users updated information
     * @param number $userId   : This is user id
     */
    public function editUser($userInfo, $userId)
    {
        $this->db->where('userId', $userId);
        $this->db->update('tbl_users', $userInfo);

        return true;
    }

    /**
     * This function is used to delete the user information.
     *
     * @param number $userId   : This is user id
     * @param mixed  $userInfo
     *
     * @return bool $result : TRUE / FALSE
     */
    public function deleteUser($userId, $userInfo)
    {
        $this->db->where('userId', $userId);
        $this->db->update('tbl_users', $userInfo);

        return $this->db->affected_rows();
    }

    /**
     * This function is used to match users password for change password.
     *
     * @param number $userId      : This is user id
     * @param mixed  $oldPassword
     */
    public function matchOldPassword($userId, $oldPassword)
    {
        $this->db->select('userId, password');
        $this->db->where('userId', $userId);
        $this->db->where('isDeleted', 0);
        $query = $this->db->get('tbl_users');

        $user = $query->result();

        if (!empty($user)) {
            if (verifyHashedPassword($oldPassword, $user[0]->password)) {
                return $user;
            }

            return [];
        }

        return [];
    }

    /**
     * This function is used to change users password.
     *
     * @param number $userId   : This is user id
     * @param array  $userInfo : This is user updation info
     */
    public function changePassword($userId, $userInfo)
    {
        $this->db->where('userId', $userId);
        $this->db->where('isDeleted', 0);
        $this->db->update('tbl_users', $userInfo);

        return $this->db->affected_rows();
    }

    /**
     * This function is used to get user login history.
     *
     * @param number $userId     : This is user id
     * @param mixed  $searchText
     * @param mixed  $fromDate
     * @param mixed  $toDate
     */
    public function loginHistoryCount($userId, $searchText, $fromDate, $toDate)
    {
        $this->db->select('BaseTbl.userId, BaseTbl.sessionData, BaseTbl.machineIp, BaseTbl.userAgent, BaseTbl.agentString, BaseTbl.platform, BaseTbl.createdAt');
        if (!empty($searchText)) {
            $likeCriteria = "(BaseTbl.sessionData LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        if (!empty($fromDate)) {
            $likeCriteria = "DATE_FORMAT(BaseTbl.createdAt, '%Y-%m-%d' ) >= '".date('Y-m-d', strtotime($fromDate))."'";
            $this->db->where($likeCriteria);
        }
        if (!empty($toDate)) {
            $likeCriteria = "DATE_FORMAT(BaseTbl.createdAt, '%Y-%m-%d' ) <= '".date('Y-m-d', strtotime($toDate))."'";
            $this->db->where($likeCriteria);
        }
        if ($userId >= 1) {
            $this->db->where('BaseTbl.userId', $userId);
        }
        $this->db->from('tbl_last_login as BaseTbl');
        $query = $this->db->get();

        return $query->num_rows();
    }

    /**
     * This function is used to get user login history.
     *
     * @param number $userId     : This is user id
     * @param number $page       : This is pagination offset
     * @param number $segment    : This is pagination limit
     * @param mixed  $searchText
     * @param mixed  $fromDate
     * @param mixed  $toDate
     *
     * @return array $result : This is result
     */
    public function loginHistory($userId, $searchText, $fromDate, $toDate, $page, $segment)
    {
        $this->db->select('BaseTbl.userId, BaseTbl.sessionData, BaseTbl.machineIp, BaseTbl.userAgent, BaseTbl.agentString, BaseTbl.platform, BaseTbl.createdAt');
        $this->db->from('tbl_last_login as BaseTbl');
        if (!empty($searchText)) {
            $likeCriteria = "(BaseTbl.sessionData  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        if (!empty($fromDate)) {
            $likeCriteria = "DATE_FORMAT(BaseTbl.createdAt, '%Y-%m-%d' ) >= '".date('Y-m-d', strtotime($fromDate))."'";
            $this->db->where($likeCriteria);
        }
        if (!empty($toDate)) {
            $likeCriteria = "DATE_FORMAT(BaseTbl.createdAt, '%Y-%m-%d' ) <= '".date('Y-m-d', strtotime($toDate))."'";
            $this->db->where($likeCriteria);
        }
        if ($userId >= 1) {
            $this->db->where('BaseTbl.userId', $userId);
        }
        $this->db->order_by('BaseTbl.id', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();

        return $query->result();
    }

    /**
     * This function used to get user information by id.
     *
     * @param number $userId : This is user id
     *
     * @return array $result : This is user information
     */
    public function getUserInfoById($userId)
    {
        $this->db->select('userId, name, email, mobile, roleId');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', 0);
        $this->db->where('userId', $userId);
        $query = $this->db->get();

        return $query->row();
    }

    /**
     * This function used to get user information by id with role.
     *
     * @param number $userId : This is user id
     *
     * @return aray $result : This is user information
     */
    public function getUserInfoWithRole($userId)
    {
        $this->db->select('BaseTbl.userId, BaseTbl.email, BaseTbl.name, BaseTbl.mobile, BaseTbl.roleId, Roles.role,BaseTbl.profileImage');
        $this->db->from('tbl_users as BaseTbl');
        $this->db->join('tbl_roles as Roles', 'Roles.roleId = BaseTbl.roleId');
        $this->db->where('BaseTbl.userId', $userId);
        $this->db->where('BaseTbl.isDeleted', 0);
        $query = $this->db->get();

        return $query->row();
    }
}
