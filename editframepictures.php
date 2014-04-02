<?php
include_once("config.php");
DEFINE ('UPLOAD_PATH', '__STONE_PICS/');
DEFINE ('MAX_UPLOAD_SIZE', 31457280);
DEFINE ('MAX_NUM_PICTURES', 6);
// DEFINE ('MAX_UPLOAD_SIZE', 15728640);

abstract class PictureType
{
    const RAWPicture = 0;
    const JPEGPicture = 1;
    const ERPPicture = 2;
    const WWWPicture = 3;
}

abstract class PictureSize
{
    const FullView = 0;     // for www picture
    const MediumView = 1;   // for www picture
    const MacroView = 2;    // for www picture
    const RawView = 3;
    const JPEGView = 4;
    const ErpView = 5;
}

/*
abstract class PictureStatus
{
    const InActive = 0;
    const Active = 1;
}*/

// helper function - groups element by given array item element
function array_group_by($arr, $key_selector) {
    $result = array();
    foreach($arr as $i){
        $key = $key_selector($i);
        if(!array_key_exists($key, $result)) {
            $result[$key] = array();
        }
        $result[$key][] = $i;
    }
    return $result;
}

function remove_element_with_value($array, $key, $value)
{
    foreach($array as $subKey => $subArray){
        if($subArray[$key] == $value){
            unset($array[$subKey]);
        }
    }
    return $array;
}

function uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, $stone_id, $imageType, $imageSize)
{
    // make sure we have file_path
    if ($tmpFilePath != "")
    {
        // setup the file path
        $newFilePath = UPLOAD_PATH . $fileName;
        // upload the file into the new location
        if (move_uploaded_file($tmpFilePath, $newFilePath))
        {
            $label = "";
            switch ($imageSize)
            {
                case PictureSize::FullView:
                    $label = "Full View";
                    break;
                case PictureSize::MediumView:
                    $label = "Medium View";
                    break;
                case PictureSize::MacroView:
                    $label = "Macro View";
                    break;
                case PictureSize::RawView:
                    $label = "RAW";
                    break;
                case PictureSize::ErpView:
                    $label = "ERP";
                    break;
                case PictureSize::JPEGView:
                    $label = "JPEG";
                    break;
                default:
                    $label = "[label]";
            }

            $thumb_nail_name = $fileName . "_THUMB";
            $sql_insert = "INSERT INTO stone_image (id_slab_frame, id_frame, id_slab, id_stone, label, type, size, status, is_linked, full_path, full_path_thumbnail, create_date, update_date) VALUES
                (".$slab_frame_id.", ".$frame_id.", ".$slab_id.", ".$stone_id.", '".$label."', ".$imageType.", ".$imageSize.",0, 0, '".$fileName."', '".$thumb_nail_name."', GETDATE(), GETDATE())";

            $_SESSION["user"]->db->insert2($sql_insert);
            if ($_SESSION["user"]->db->numRows() > 0)
            {
                return true;
            } else
            {
                return false;
            }
        } else
        {
            $error = "Saving data failed.";
            return false;
        }
    }
}

/*
function randString($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
{
    $str = '';
    $count = strlen($charset);
    while ($length--) {
        $str .= $charset[mt_rand(0, $count-1)];
    }
    return $str;
}*/

function isImageNameValid($imageName)
{
    if (!empty($imageName))
    {
        $sql_query = "SELECT id FROM stone_image WHERE full_path LIKE '".$imageName."'";
        $_SESSION["user"]->db->select($sql_query);

        if ($_SESSION["user"]->db->numRows() > 0)
            return false;
        else
            return true;
    }
    return true;
}

function checkFileError($error)
{
    switch ($error) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    return true;

}

function validateOnSubmit()
{
    // check names of all the pictures, if any of them is duplicate vs database return error - OK
    // check names of all the pictures - store in array and find if there are any duplicates - OK
    // check sizes of each file before submit
    // check total size of the request and its content

    $error_duplicate = '';
    $error_size_limit = '';

    $file_names = array();

    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::RAWPicture]['name']); $i++)
    {
        $fileName = $_FILES['picture_to_upload_'.PictureType::RAWPicture]['name'][$i];
        if (!empty($fileName))
        {
            if (!isImageNameValid($fileName))
            {
                $error_duplicate = $error_duplicate . 'File with name ' . $fileName . ' already exists <br />';
            } else {
                $file_names[] = $fileName;
                if (checkFileError($_FILES['picture_to_upload_'.PictureType::RAWPicture]['error'][$i]))
                {
                    $file_size = $_FILES['picture_to_upload_'.PictureType::RAWPicture]['size'][$i];
                    if ($file_size > MAX_UPLOAD_SIZE)
                    {
                        $totalMBytes = ($file_size/1024)/1024;
                        $error_size_limit = $error_size_limit . 'The size of file '. $fileName .' exceeds allowed limit of ' . round($totalMBytes) . ' MB <br />';
                    }
                }
            }
        }
    }

    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::JPEGPicture]['name']); $i++)
    {
        $fileName = $_FILES['picture_to_upload_'.PictureType::JPEGPicture]['name'][$i];
        if (!empty($fileName))
        {
            if (!isImageNameValid($fileName))
            {
                $error_duplicate = $error_duplicate . 'File with name ' . $fileName . ' already exists <br />';
            } else {
                $file_names[] = $fileName;
                if (checkFileError($_FILES['picture_to_upload_'.PictureType::JPEGPicture]['error'][$i]))
                {
                    $file_size = $_FILES['picture_to_upload_'.PictureType::JPEGPicture]['size'][$i];
                    if ($file_size > MAX_UPLOAD_SIZE)
                    {
                        $totalMBytes = ($file_size/1024)/1024;
                        $error_size_limit = $error_size_limit . 'The size of file '. $fileName .' exceeds allowed limit of ' . round($totalMBytes) . ' MB <br />';
                    }
                }
            }
        }
    }

    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::ERPPicture]['name']); $i++)
    {
        $fileName = $_FILES['picture_to_upload_'.PictureType::ERPPicture]['name'][$i];
        if (!empty($fileName))
        {
            if (!isImageNameValid($fileName))
            {
                $error_duplicate = $error_duplicate . 'File with name ' . $fileName . ' already exists <br />';
            } else {
                $file_names[] = $fileName;
                if (checkFileError($_FILES['picture_to_upload_'.PictureType::ERPPicture]['error'][$i]))
                {
                    $file_size = $_FILES['picture_to_upload_'.PictureType::ERPPicture]['size'][$i];
                    if ($file_size > MAX_UPLOAD_SIZE)
                    {
                        $totalMBytes = ($file_size/1024)/1024;
                        $error_size_limit = $error_size_limit . 'The size of file '. $fileName .' exceeds allowed limit of ' . round($totalMBytes) . ' MB <br />';
                    }
                }
            }
        }
    }

    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::FullView]['name']); $i++)
    {
        $fileName = $_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::FullView]['name'][$i];
        if (!empty($fileName))
        {
            if (!isImageNameValid($fileName))
            {
                $error_duplicate = $error_duplicate . 'File with name ' . $fileName . ' already exists <br />';
            } else {
                $file_names[] = $fileName;
                if (checkFileError($_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::FullView]['error'][$i]))
                {
                    $file_size = $_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::FullView]['size'][$i];
                    if ($file_size > MAX_UPLOAD_SIZE)
                    {
                        $totalMBytes = ($file_size/1024)/1024;
                        $error_size_limit = $error_size_limit . 'The size of file '. $fileName .' exceeds allowed limit of ' . round($totalMBytes) . ' MB <br />';
                    }
                }
            }
        }
    }

    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MediumView]['name']); $i++)
    {
        $fileName = $_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MediumView]['name'][$i];
        if (!empty($fileName))
        {
            if (!isImageNameValid($fileName))
            {
                $error_duplicate = $error_duplicate . 'File with name ' . $fileName . ' already exists <br />';
            } else {
                $file_names[] = $fileName;
                if (checkFileError($_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MediumView]['error'][$i]))
                {
                    $file_size = $_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MediumView]['size'][$i];
                    if ($file_size > MAX_UPLOAD_SIZE)
                    {
                        $totalMBytes = ($file_size/1024)/1024;
                        $error_size_limit = $error_size_limit . 'The size of file '. $fileName .' exceeds allowed limit of ' . round($totalMBytes) . ' MB <br />';
                    }
                }
            }
        }
    }

    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MacroView]['name']); $i++)
    {
        $fileName = $_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MacroView]['name'][$i];
        if (!empty($fileName))
        {
            if (!isImageNameValid($fileName))
            {
                $error_duplicate = $error_duplicate . 'File with name ' . $fileName . ' already exists <br />';
            } else {
                $file_names[] = $fileName;
                if (checkFileError($_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MacroView]['error'][$i]))
                {
                    $file_size = $_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MacroView]['size'][$i];
                    if ($file_size > MAX_UPLOAD_SIZE)
                    {
                        $totalMBytes = ($file_size/1024)/1024;
                        $error_size_limit = $error_size_limit . 'The size of file '. $fileName .' exceeds allowed limit of ' . round($totalMBytes) . ' MB <br />';
                    }
                }
            }
        }
    }

    // check if array file_names contains duplicates
    $new_array = array();
    foreach ($file_names as $key => $value) {
        if(isset($new_array[$value]))
            $new_array[$value] += 1;
        else
            $new_array[$value] = 1;
    }
    foreach ($new_array as $name => $n) {
        if($n > 1)
            $error_duplicate = $error_duplicate = $error_duplicate . ' File with name ' . $name . ' is duplicated.'. '<br />';
    }

    /*
    if(count(array_unique($file_names)) < count($file_names))
    {
        // array has duplicates
        $error_duplicate = $error_duplicate = $error_duplicate . ' ';
    }
    else
    {
        // array does not have duplicates
    }
    */

    if (!empty($error_duplicate))
        throw new RuntimeException($error_duplicate);
    if (!empty($error_size_limit))
        throw new RuntimeException($error_size_limit);
}

//////////////////////////////////////////////////////////////////////////////////////////////
$error = "";
if (!isset($_GET['slab_id']) || !is_numeric($_GET['slab_id']))
    $_GET['slab_id'] = 0;
if (!isset($_GET['frame_id']) || !is_numeric($_GET['frame_id']))
    $_GET['frame_id'] = 0;
if (!isset($_GET['slab_frame_id']) || !is_numeric($_GET['slab_frame_id']))
    $_GET['slab_frame_id'] = 0;
if (!isset($_GET['stone_id']) || !is_numeric($_GET['stone_id']))
    $_GET['stone_id'] = 0;

// postback parameters
if ($_GET['slab_id'] == 0 && (isset($_POST['slab_id']) && is_numeric($_POST['slab_id'])))
    $_GET['slab_id'] = (int)$_POST['slab_id'];

if ($_GET['frame_id'] == 0 && (isset($_POST['frame_id']) && is_numeric($_POST['frame_id'])))
    $_GET['frame_id'] = (int)$_POST['frame_id'];

if ($_GET['slab_frame_id'] == 0 && (isset($_POST['slab_frame_id']) && is_numeric($_POST['slab_frame_id'])))
    $_GET['slab_frame_id'] = (int)$_POST['slab_frame_id'];

if ($_GET['stone_id'] == 0 && (isset($_POST['stone_id']) && is_numeric($_POST['stone_id'])))
    $_GET['stone_id'] = (int)$_POST['stone_id'];

$slab_id = (int)$_GET['slab_id'];
$frame_id = (int)$_GET['frame_id'];
$slab_frame_id = (int)$_GET['slab_frame_id'];
$stone_id = (int)$_GET['stone_id'];

// deleting pictures
if (isset($_GET['act'])) {

    $act = $_GET['act'];
    switch ($act)
    {
        case "del" :
            if (!isset($_GET['id']) || !is_numeric($_GET['id']))
                $_GET['id'] = 0;

            $id = (int)$_GET['id'];
            $query = 'SELECT id, full_path FROM stone_image WHERE id='.$id;
            $_SESSION["user"]->db->select($query);
            $image = $_SESSION["user"]->db->fetchArray();
            // TODO: delete from hdd
            if (!empty($image['full_path'])) {
                $filePath = UPLOAD_PATH.$image['full_path'];
                if (is_file($filePath)) {
                    // remove if file exists
                    unlink($filePath);
                }
            }
            // removing from db
            $query = 'DELETE FROM stone_image WHERE id='.$id.'';
            $_SESSION["user"]->db->delete('stone_image', $id, $query);
            break;
        case "update":
            break;

    }
}
// end - deleting

// checking the count of status=1 (checked) items for each picture type
// if (count($_POST['erp_picture_status']) > 3) {$error = "Number of checked erp images is greater than 3"; $flag = 1;}
// if (count($_POST['www_picture_status']) > 3) {$error = "Number of checked www images is greater than 3"; $flag = 1;}

//if (error != "")
//{
//    // update status
//   if($flag != 1)
//    {

        /*
        for ($i = 0; $i < count($_POST['id']); $i++)
        {
            $id = (int)$_POST['id'][$i];
            if ($id != 0) {
                if (in_array($id, $_POST['erp_picture_status']) || in_array($id, $_POST['www_picture_status']) || in_array($id, $_POST['raw_picture_status']))
                {
                    $sql_update = 'UPDATE [stone_image] SET status = 1, update_date = GETDATE() WHERE id ='.$id;
                    if(!$_SESSION["user"]->db->update('[stone_image]', $id, $sql_update))
                    {
                        $error = "Saving data failed.";
                    }
                } else
                {
                    $sql_update = 'UPDATE stone_image SET status = 0, update_date = GETDATE() WHERE id ='.$id;
                    if(!$_SESSION["user"]->db->update('stone_image', $id, $sql_update))
                    {
                        $error = "Saving data failed.";
                    }
                }
            }
        }*/
//    }

try
{
    // check content length
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0)
    {
        $totalBytes = (int)$_SERVER['CONTENT_LENGTH'];
        $totalMBytes = ($totalBytes/1024)/1024;

        throw new RuntimeException(sprintf('The server was unable to handle that much data (%s bytes) due to its current configuration %s Please upload only one file each time.',  round($totalMBytes), '<br />'));
    }

    validateOnSubmit();

    // first need to check the submit form methof
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        // update status if changed (onlyt when post)
        $need_pics_status = $_POST['need_pics_status'];
        $need_pics_status_db = 0;

        $sql_check_status = "SELECT si.status FROM stone_image si WHERE id_slab_frame=".$slab_frame_id." and id_frame = ".$frame_id." and id_slab = ".$slab_id."GROUP BY si.status";
        // query status check
        $_SESSION["user"]->db->select($sql_check_status);
        $rs = $_SESSION["user"]->db->fetchArray();
        $need_pics_status_db = (int)$rs[0];

        $need_pics_status_val = 0;
        if (isset($_POST['need_pics_status']))
            $need_pics_status_val = 1;

        if (($need_pics_status_val == 1 && $need_pics_status_db == 0)
            ||($need_pics_status_val == 0 && $need_pics_status_db == 1))
        {
            // needs update
            $sql_update = 'UPDATE stone_image SET status = '.$need_pics_status_val.', update_date = GETDATE() WHERE id_slab ='.$slab_id.' AND id_stone='.$stone_id;
            if(!$_SESSION["user"]->db->update('stone_image', 0, $sql_update))
            {
                $error = "Saving data failed.";
                throw new RuntimeException($error);
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////
    // ------------------ raw pictures do not have size property in its name
    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::RAWPicture]['name']); $i++)
    {
        // get the temp file path
        $fileName = $_FILES['picture_to_upload_'.PictureType::RAWPicture]['name'][$i];
        if (!empty($fileName))
        {
            $tmpFilePath = $_FILES['picture_to_upload_'.PictureType::RAWPicture]['tmp_name'][$i];
            if (isImageNameValid($fileName))
                uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, $stone_id, PictureType::RAWPicture, PictureSize::RawView);
            else
                $error = 'File with the name '.$fileName.' already exists';
        }
    }
    // adding jpg picture
    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::JPEGPicture]['name']); $i++)
    {
        // get the temp file path
        $fileName = $_FILES['picture_to_upload_'.PictureType::JPEGPicture]['name'][$i];
        if (!empty($fileName))
        {
            $tmpFilePath = $_FILES['picture_to_upload_'.PictureType::JPEGPicture]['tmp_name'][$i];
            if (isImageNameValid($fileName))
                uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, $stone_id, PictureType::JPEGPicture, PictureSize::JPEGView);
            else
                $error = 'File with the name '.$fileName.' already exists';
        }
    }

    // adding erp picture
    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::ERPPicture]['name']); $i++)
    {
        // get the temp file path
        $fileName = $_FILES['picture_to_upload_'.PictureType::ERPPicture]['name'][$i];
        if (!empty($fileName))
        {
            $tmpFilePath = $_FILES['picture_to_upload_'.PictureType::ERPPicture]['tmp_name'][$i];
            if (isImageNameValid($fileName))
                uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, $stone_id, PictureType::ERPPicture, PictureSize::ErpView);
            else
                $error = 'File with the name '.$fileName.' already exists';
        }
    }

    // ---------------- adding www images
    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::FullView]['name']); $i++)
    {
        $fileName = $_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::FullView]['name'][$i];
        if (!empty($fileName))
        {
            $tmpFilePath = $_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::FullView]['tmp_name'][$i];
            if (isImageNameValid($fileName))
                uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, $stone_id, PictureType::WWWPicture, PictureSize::FullView);
            else
                $error = 'File with the name '.$fileName.' already exists';
        }
    }
    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MacroView]['name']); $i++)
    {
        $fileName = $_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MacroView]['name'][$i];
        if (!empty($fileName))
        {
            $tmpFilePath = $_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MacroView]['tmp_name'][$i];
            if (isImageNameValid($fileName))
                uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, $stone_id, PictureType::WWWPicture, PictureSize::MacroView);
            else
                $error = 'File with the name '.$fileName.' already exists';
        }
    }
    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MediumView]['name']); $i++)
    {
        $fileName = $_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MediumView]['name'][$i];
        if (!empty($fileName))
        {
            $tmpFilePath = $_FILES['picture_to_upload_'.PictureType::WWWPicture.'_'.PictureSize::MediumView]['tmp_name'][$i];
            if (isImageNameValid($fileName))
                uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, $stone_id, PictureType::WWWPicture, PictureSize::MediumView);
            else
                $error = 'File with the name '.$fileName.' already exists';
        }
    }
} catch (RuntimeException $e)
{
    $error = $e->getMessage();
}

///////////////////////////////////////////////////////////////////////
// display results
// if (empty($error))
{
    if($slab_frame_id != 0)
    {
        $sql_stone = 'SELECT st.id, st.name, sf.id_frame, f.sign AS "frame_sign", l.name AS "location_name" FROM slab_frame sf
                INNER JOIN frame f ON f.id=sf.id_frame
                INNER JOIN slab s ON s.id=sf.id_slab
                INNER JOIN stone st ON st.id=s.id_stone
                INNER JOIN location l ON s.id_location = l.id
            WHERE sf.id_slab='.$slab_id.' AND sf.id_frame='.$frame_id;
    }
    else
    {
        $sql_stone = 'SELECT st.id, st.name, sf.id_frame, f.sign AS "frame_sign", l.name AS "location_name" FROM slab_frame_duplicate sf
                INNER JOIN frame f ON f.id=sf.id_frame
                INNER JOIN slab s ON s.id=sf.id_slab
                INNER JOIN stone st ON st.id=s.id_stone
                INNER JOIN location l ON s.id_location = l.id
            WHERE sf.id_slab='.$slab_id.' AND sf.id_frame='.$frame_id;
    }

    // query stone info
    $_SESSION["user"]->db->select($sql_stone);
    $stone = $_SESSION["user"]->db->fetchArray();

    $sql_pictures = "SELECT si.id, si.id_slab_frame, si.id_frame, si.id_slab, si.label, si.type, si.size, si.status, si.is_linked, '" .UPLOAD_PATH. "' + si.full_path as 'full_path', '"
        . UPLOAD_PATH. "' + si.full_path_thumbnail AS 'full_path_thumbnail' from stone_image si WHERE id_slab_frame=".$slab_frame_id." and id_frame = ".$frame_id." and id_slab = ".$slab_id
        . " UNION SELECT 0 as id, 0 as id_slab_frame, 0 as id_frame, 0 as id_slab, 'Full View' as label, 3 as type, 0 as size, 0 as status, 0 as is_linked, '' as full_path, '' as full_path_thumbnail"
        . " UNION SELECT 0 as id, 0 as id_slab_frame, 0 as id_frame, 0 as id_slab, 'Medium View' as label, 3 as type, 1 as size, 0 as status, 0 as is_linked, '' as full_path, '' as full_path_thumbnail"
        . " UNION SELECT 0 as id, 0 as id_slab_frame, 0 as id_frame, 0 as id_slab, 'Macro View' as label, 3 as type, 2 as size, 0 as status, 0 as is_linked, '' as full_path, '' as full_path_thumbnail"

        . " UNION SELECT 0 as id, 0 as id_slab_frame, 0 as id_frame, 0 as id_slab, 'ERP' as label, 2 as type, 5 as size, 0 as status, 0 as is_linked, '' as full_path, '' as full_path_thumbnail"
        . " UNION SELECT 0 as id, 0 as id_slab_frame, 0 as id_frame, 0 as id_slab, 'JPEG' as label, 1 as type, 4 as size, 0 as status, 0 as is_linked, '' as full_path, '' as full_path_thumbnail"
        . " UNION SELECT 0 as id, 0 as id_slab_frame, 0 as id_frame, 0 as id_slab, 'RAW' as label, 0 as type, 3 as size, 0 as status, 0 as is_linked, '' as full_path, '' as full_path_thumbnail"
        . " ORDER BY id_slab_frame, si.id_frame, size"
    ;

    // query pictures info
    $_SESSION["user"]->db->select($sql_pictures);
    $pictures = $_SESSION["user"]->db->fetchAllArrays();

    // grouped by type
    $pics_grouped = array_group_by($pictures, function($i) {return $i[5];});
    $pics_raw = $pics_grouped[PictureType::RAWPicture];
    $pics_jpeg = $pics_grouped[PictureType::JPEGPicture];
    $pics_erp = $pics_grouped[PictureType::ERPPicture];
    $pics_www = $pics_grouped[PictureType::WWWPicture];

    if (count($pics_raw) > 1)
    {
        $pics_raw = remove_element_with_value($pics_raw, "id", 0);
        $pics_raw = array_values($pics_raw);
    }

    // only one jpeg picture allowed
    if (count($pics_jpeg) > 1)
    {
        $pics_jpeg = remove_element_with_value($pics_jpeg, "id", 0);
        $pics_jpeg = array_values($pics_jpeg);
    }

    // only one erp picture
    if (count($pics_erp) > 1)
    {
        $pics_erp = remove_element_with_value($pics_erp, "id", 0);
        $pics_erp = array_values($pics_erp);
    }

    $pics_erp_final = array();
    if (count($pics_erp) > 3)
    {
        $pics_erp_by_size = array_group_by($pics_erp, function($i) {return $i[6];});
        foreach ($pics_erp_by_size as $type)
        {
            $count = count($type);
            if ($count > 1)
            {
                $type = remove_element_with_value($type, "id", 0);
                $type = array_values($type);
            }
            $pics_erp_final = array_merge($pics_erp_final, $type);
        }
        $pics_erp = $pics_erp_final;
    }

    $pics_www_final = array();
    if (count($pics_www) > 3)
    {
        $pics_www_by_size = array_group_by($pics_www, function($i) {return $i[6];});
        foreach ($pics_www_by_size as $type)
        {
            $count = count($type);
            if ($count > 1)
            {
                $type = remove_element_with_value($type, "id", 0);
                $type = array_values($type);
            }
            $pics_www_final = array_merge($pics_www_final, $type);
        }
        $pics_www = $pics_www_final;
    }

    // if uploaded every picture enable the checkbox
    $sql_pics_count = "SELECT COUNT(si.id) FROM stone_image si WHERE id_slab_frame=".$slab_frame_id." and id_frame = ".$frame_id." and id_slab = ".$slab_id;

    $_SESSION["user"]->db->select($sql_pics_count);
    $pics_count = $_SESSION["user"]->db->fetchArray();
    $pics_count = (int)$pics_count[0];

    $need_more_pics_disabled = true;
    if ($pics_count >= MAX_NUM_PICTURES)
        $need_more_pics_disabled = false;

    // get value of needs_more_pics (0 or 1)
    $need_pics_status_val = $pictures[count($pictures) - 1]["status"];


    /*
    if (!$need_more_pics_disabled)
    {
        // when enabled display status
        $need_pics_status = $pictures[0]["status"];
    }
    else
    {
        // then disabled check the 0 value and update if necessary
        $need_pics_status = $pics_raw[0]["status"];
        if ($need_pics_status == 1)
        {
            $sql_update = 'UPDATE stone_image SET status = 0, update_date = GETDATE() WHERE id_slab ='.$slab_id.' AND id_stone='.$stone_id;
            if(!$_SESSION["user"]->db->update('stone_image', 0, $sql_update))
            {
                $error = "Saving data failed.";
            }

        }
    }
    */

    include('Smarty.class.php');
    $smarty = new Smarty;

    $smarty->assign('slab_id', $slab_id);
    $smarty->assign('frame_id', $frame_id);
    $smarty->assign('slab_frame_id', $slab_frame_id);
    $smarty->assign('stone_id', $stone_id);
    $smarty->assign('need_more_pics_disabled', $need_more_pics_disabled);
    $smarty->assign('need_pics_status_val', $need_pics_status_val);

    $smarty->assign('stone', $stone);
    $smarty->assign('pics_raw', $pics_raw);
    $smarty->assign('pics_jpeg', $pics_jpeg);
    $smarty->assign('pics_erp', $pics_erp);
    $smarty->assign('pics_www', $pics_www);
    $smarty->assign('error',$error);

    $smarty->assign('script', 'editframepictures');
    $smarty->display('editframepictures.tpl');
}
