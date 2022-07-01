<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'login';
$route['404_override'] = 'error_404';
$route['translate_uri_dashes'] = false;

// USER DEFINED ROUTES

$route['loginMe'] = 'login/loginMe';
$route['dashboard'] = 'user';
$route['logout'] = 'user/logout';
$route['userListing'] = 'user/userListing';
$route['userListing/(:num)'] = 'user/userListing/$1';
$route['addNew'] = 'user/addNew';
$route['addNewUser'] = 'user/addNewUser';
$route['editOld'] = 'user/editOld';
$route['editOld/(:num)'] = 'user/editOld/$1';
$route['editUser'] = 'user/editUser';
$route['deleteUser'] = 'user/deleteUser';
$route['profile'] = 'user/profile';
$route['profile/(:any)'] = 'user/profile/$1';
$route['profileUpdate'] = 'user/profileUpdate';
$route['profileUpdate/(:any)'] = 'user/profileUpdate/$1';

$route['loadChangePass'] = 'user/loadChangePass';
$route['changePassword'] = 'user/changePassword';
$route['changePassword/(:any)'] = 'user/changePassword/$1';
$route['pageNotFound'] = 'user/pageNotFound';
$route['checkEmailExists'] = 'user/checkEmailExists';
$route['login-history'] = 'user/loginHistoy';
$route['login-history/(:num)'] = 'user/loginHistoy/$1';
$route['login-history/(:num)/(:num)'] = 'user/loginHistoy/$1/$2';

$route['forgotPassword'] = 'login/forgotPassword';
$route['resetPasswordUser'] = 'login/resetPasswordUser';
$route['resetPasswordConfirmUser'] = 'login/resetPasswordConfirmUser';
$route['resetPasswordConfirmUser/(:any)'] = 'login/resetPasswordConfirmUser/$1';
$route['resetPasswordConfirmUser/(:any)/(:any)'] = 'login/resetPasswordConfirmUser/$1/$2';
$route['createPasswordUser'] = 'login/createPasswordUser';

$route['categoryListing'] = 'category/categoryListing';
$route['categoryListing/(:num)'] = 'category/categoryListing/$1';
$route['addCategory'] = 'category/addNew';
$route['addNewCategory'] = 'category/addNewCategory';
$route['editCategory/(:num)'] = 'category/editCategory/$1';
$route['editCategoryPost'] = 'category/editCategoryPost';
$route['deleteCategory'] = 'category/deleteCategory';

$route['imageListing'] = 'images/imagesListing';
$route['imageListing/(:num)'] = 'images/imagesListing/$1';
$route['addImage'] = 'images/addNew';
$route['addNewImage'] = 'images/addNewImage';
$route['editImage/(:num)'] = 'images/editImage/$1';
$route['editImagePost'] = 'images/editImagePost';
$route['deleteImage'] = 'images/deleteImage';

$route['adsListing'] = 'ads/adsListing';
$route['adsListing/(:num)'] = 'ads/adsListing/$1';
$route['addAds'] = 'ads/addNew';
$route['addNewAds'] = 'ads/addNewAds';
$route['editAds/(:num)'] = 'ads/editAds/$1';
$route['editAdsPost'] = 'ads/editAdsPost';
$route['deleteAds'] = 'ads/deleteAds';

//API
$route['api/login'] = 'Api/User/login';
$route['api/register'] = 'Api/User/register';
$route['api/profile'] = 'Api/User/profile';
$route['api/profileUpdate'] = 'Api/User/profileUpdate';
$route['api/updatefirebaseId'] = 'Api/User/updatefirebaseId';
$route['api/allcategories'] = 'Api/Category/categories';
$route['api/addusercategories'] = 'Api/UserCategory/addusercategories';
$route['api/updateusercategories'] = 'Api/UserCategory/updateusercategories';
$route['api/userLikedCategories'] = 'Api/UserCategory/usercategories';
$route['api/userlikedcategoriesimages'] = 'Api/UserCategory/userLikedCategoryImages';
$route['api/allImages'] = 'Api/Images/allImages';
$route['api/imagesByCategory/(:num)'] = 'Api/Images/imagesByCategory/$1';
$route['api/addImageLike'] = 'Api/Images/imageLike';
$route['api/dislikeImage'] = 'Api/Images/imagedisLike';
$route['api/getUserLikedImages'] = 'Api/Images/userLikedImages';
$route['api/getUserLikedImagesByCategory/(:num)'] = 'Api/Images/userLikedImagesByCategory/$1';
$route['api/getMostLikedImages'] = 'Api/Images/getMostUserLikedImages';
$route['api/getMostLikedImagesByCategory/(:num)'] = 'Api/Images/getMostUserLikedImagesByCategory/$1';
$route['api/getRecentelyAddedImages'] = 'Api/Images/getRecentelyAddedImages';
$route['api/getRecentelyAddedImagesByCategory/(:num)'] = 'Api/Images/getRecentelyAddedImagesByCategory/$1';
$route['api/forgotPasswordUser'] = 'Api/User/resetPasswordUser';
$route['api/socialLogin'] = 'Api/User/socialLogin';
$route['api/allAds'] = 'Api/Ads/allAds';
$route['api/searchImages'] = 'Api/Images/getImagesBYSearch';
$route['api/addDevice'] = 'Api/User/addDevice';
$route['api/deleteDevice'] = 'Api/User/deleteDevice';


// End of file routes.php
// Location: ./application/config/routes.php
