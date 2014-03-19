<?php
include_once("config.php");
DEFINE ('UPLOAD_PATH', '__STONE_PICS/');

abstract class PictureType
{
    const RAWPicture = 0;
    const ERPPicture = 1;
    const WWWPicture = 2;
}

abstract class PictureSize
{
    const FullView = 0;
    const MediumView = 1;
    const MacroView = 2;
}

abstract class PictureStatus
{
    const InActive = 0;
    const Active = 1;
}

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

function uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, $imageType, $imageSize)
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
                default:
                    $label = "[label]";
            }
            $thumb_nail_name = $fileName . "_THUMB";
            $sql_insert = "INSERT INTO stone_image (id_slab_frame, id_frame, id_slab, label, type, size, status, is_linked, full_path, full_path_thumbnail, create_date, update_date) VALUES
                (".$slab_frame_id.", ".$frame_id.", ".$slab_id.", '".$label."', ".$imageType.", ".$imageSize.",0, 0, '".$fileName."', '".$thumb_nail_name."', GETDATE(), GETDATE())";

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

function randString($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
{
    $str = '';
    $count = strlen($charset);
    while ($length--) {
        $str .= $charset[mt_rand(0, $count-1)];
    }
    return $str;
}

function isImageNameValid($imageName)
{
    if ($imageName != '')
    {
        $sql_query = "SELECT id FROM stone_image WHERE full_path LIKE '".$imageName."'";
        $_SESSION["user"]->db->select($sql_query);

        if ($_SESSION["user"]->db->numRows() > 0)
            return false;
        else
            return true;
    }
    return false;
}

$error = "";
if (!isset($_GET['slab_id']) || !is_numeric($_GET['slab_id']))
    $_GET['slab_id'] = 0;
if (!isset($_GET['frame_id']) || !is_numeric($_GET['frame_id']))
    $_GET['frame_id'] = 0;
if (!isset($_GET['slab_frame_id']) || !is_numeric($_GET['slab_frame_id']))
    $_GET['slab_frame_id'] = 0;

// postback parameters
if ($_GET['slab_id'] == 0 && (isset($_POST['slab_id']) && is_numeric($_POST['slab_id'])))
    $_GET['slab_id'] = (int)$_POST['slab_id'];

if ($_GET['frame_id'] == 0 && (isset($_POST['frame_id']) && is_numeric($_POST['frame_id'])))
    $_GET['frame_id'] = (int)$_POST['frame_id'];

if ($_GET['slab_frame_id'] == 0 && (isset($_POST['slab_frame_id']) && is_numeric($_POST['slab_frame_id'])))
    $_GET['slab_frame_id'] = (int)$_POST['slab_frame_id'];

$slab_id = (int)$_GET['slab_id'];
$frame_id = (int)$_GET['frame_id'];
$slab_frame_id = (int)$_GET['slab_frame_id'];

// deleting pictures
if (isset($_GET['act'])) {

    $act = $_GET['act'];
    switch ($act)
    {
        case "del" :
            if (!isset($_GET['id']) || !is_numeric($_GET['id']))
                $_GET['id'] = 0;

            $id = (int)$_GET['id'];
            // TODO: delete from hdd
            $query = 'DELETE FROM stone_image WHERE id='.$id.'';
            $_SESSION["user"]->db->delete('stone_image', $id, $query);
            break;
        case "update":
            break;

    }
}
// end - deleting

// checking the count of status=1 (checked) items for each picture type
if (count($_POST['erp_picture_status']) > 3) {$error = "Number of checked erp images is greater than 3";}
if (count($_POST['www_picture_status']) > 3) {$error = "Number of checked www images is greater than 3";}

if (error != "")
{
    // update status
    for ($i = 0; $i < count($_POST['id']); $i++)
    {
        $id = (int)$_POST['id'][$i];
        if ($id != 0) {
            if (in_array($id, $_POST['erp_picture_status']) || in_array($id, $_POST['www_picture_status']))
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
    }

    // ------------------ raw pictures do not have size property in its name
    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::RAWPicture]['name']); $i++)
    {
        // get the temp file path
        $fileName = $_FILES['picture_to_upload_'.PictureType::RAWPicture]['name'][$i];
        if (!empty($fileName))
        {
            $tmpFilePath = $_FILES['picture_to_upload_'.PictureType::RAWPicture]['tmp_name'][$i];
            if (isImageNameValid($fileName))
                uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, PictureType::RAWPicture, null);
            else
                $error = 'File with the name '.$fileName.' already exists';
        }
    }
    // ------------------ adding erp images
    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::ERPPicture.'_'.PictureSize::FullView]['name']); $i++)
    {
        // get the temp file path
        $fileName = $_FILES['picture_to_upload_'.PictureType::ERPPicture.'_'.PictureSize::FullView]['name'][$i];
        if (!empty($fileName))
        {
            $tmpFilePath = $_FILES['picture_to_upload_'.PictureType::ERPPicture.'_'.PictureSize::FullView]['tmp_name'][$i];
            if (isImageNameValid($fileName))
                uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, PictureType::ERPPicture, PictureSize::FullView);
            else
                $error = 'File with the name '.$fileName.' already exists';
        }
    }
    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::ERPPicture.'_'.PictureSize::MacroView]['name']); $i++)
    {
        $fileName = $_FILES['picture_to_upload_'.PictureType::ERPPicture.'_'.PictureSize::MacroView]['name'][$i];
        if (!empty($fileName))
        {
            $tmpFilePath = $_FILES['picture_to_upload_'.PictureType::ERPPicture.'_'.PictureSize::MacroView]['tmp_name'][$i];
            if (isImageNameValid($fileName))
                uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, PictureType::ERPPicture, PictureSize::MacroView);
            else
                $error = 'File with the name '.$fileName.' already exists';
        }
    }
    for ($i = 0; $i < count($_FILES['picture_to_upload_'.PictureType::ERPPicture.'_'.PictureSize::MediumView]['name']); $i++)
    {
        $fileName = $_FILES['picture_to_upload_'.PictureType::ERPPicture.'_'.PictureSize::MediumView]['name'][$i];
        if (!empty($fileName))
        {
            $tmpFilePath = $_FILES['picture_to_upload_'.PictureType::ERPPicture.'_'.PictureSize::MediumView]['tmp_name'][$i];
            if (isImageNameValid($fileName))
                uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, PictureType::ERPPicture, PictureSize::MediumView);
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
                uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, PictureType::WWWPicture, PictureSize::FullView);
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
                uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, PictureType::WWWPicture, PictureSize::MacroView);
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
                uploadImage($fileName, $tmpFilePath, $slab_frame_id, $slab_id, $frame_id, PictureType::WWWPicture, PictureSize::MediumView);
            else
                $error = 'File with the name '.$fileName.' already exists';
        }
    }
}

    // when postback we use stone_id instead of frame and slab
    //if ($stone_id == 0) {
        // get stone information by slab_id and frame_id
        $sql_stone = 'SELECT st.id, st.name, sf.id_frame, f.sign AS "frame_sign", l.name AS "location_name" FROM slab_frame sf
            INNER JOIN frame f ON f.id=sf.id_frame
            INNER JOIN slab s ON s.id=sf.id_slab
            INNER JOIN stone st ON st.id=s.id_stone
            INNER JOIN location l ON s.id_location = l.id
        WHERE sf.id_slab='.$slab_id.' AND sf.id_frame='.$frame_id;

    //}
    /*
    else {
        // get stone information by stone_id
        $sql_stone = 'SELECT st.id, st.name, sf.id_frame, f.sign AS "frame_sign", l.name AS "location_name" FROM slab_frame sf
            INNER JOIN frame f ON f.id=sf.id_frame
            INNER JOIN slab s ON s.id=sf.id_slab
            INNER JOIN stone st ON st.id=s.id_stone
            INNER JOIN location l ON s.id_location = l.id
        WHERE st.id='.$stone_id;
    }*/

    // query stone info
    $_SESSION["user"]->db->select($sql_stone);
    $stone = $_SESSION["user"]->db->fetchArray();

    // get picture information by stone id
    $sql_pictures = "SELECT si.id, si.id_slab_frame, si.id_frame, si.id_slab, si.label, si.type, si.size, si.status, si.is_linked, si.full_path, "
        . "'__STONE_PICS/' + si.full_path_thumbnail AS 'full_path_thumbnail' from stone_image si WHERE id_slab_frame=".$slab_frame_id
        . " UNION SELECT 0 as id, 0 as id_slab_frame, 0 as id_frame, 0 as id_slab, 'Full View' as label, 1 as type, 0 as size, 0 as status, 0 as is_linked, '' as full_path, '' as full_path_thumbnail"
        . " UNION SELECT 0 as id, 0 as id_slab_frame, 0 as id_frame, 0 as id_slab, 'Medium View' as label, 1 as type, 1 as size, 0 as status, 0 as is_linked, '' as full_path, '' as full_path_thumbnail"
        . " UNION SELECT 0 as id, 0 as id_slab_frame, 0 as id_frame, 0 as id_slab, 'Macro View' as label, 1 as type, 2 as size, 0 as status, 0 as is_linked, '' as full_path, '' as full_path_thumbnail"
        . " UNION SELECT 0 as id, 0 as id_slab_frame, 0 as id_frame, 0 as id_slab, 'Full View' as label, 2 as type, 0 as size, 0 as status, 0 as is_linked, '' as full_path, '' as full_path_thumbnail"
        . " UNION SELECT 0 as id, 0 as id_slab_frame, 0 as id_frame, 0 as id_slab, 'Medium View' as label, 2 as type, 1 as size, 0 as status, 0 as is_linked, '' as full_path, '' as full_path_thumbnail"
        . " UNION SELECT 0 as id, 0 as id_slab_frame, 0 as id_frame, 0 as id_slab, 'Macro View' as label, 2 as type, 2 as size, 0 as status, 0 as is_linked, '' as full_path, '' as full_path_thumbnail"
        . " UNION SELECT 0 as id, 0 as id_slab_frame, 0 as id_frame, 0 as id_slab, 'RAW' as label, 0 as type, 2 as size, 0 as status, 0 as is_linked, '' as full_path, '' as full_path_thumbnail ORDER BY id_slab_frame, size"
    ;
    // TODO: make union to display new upload items

    // query pictures info
    $_SESSION["user"]->db->select($sql_pictures);
    $pictures = $_SESSION["user"]->db->fetchAllArrays();

    // grouped by type
    $pics_grouped = array_group_by($pictures, function($i) {return $i[5];});
    $pics_raw = $pics_grouped[PictureType::RAWPicture];
    $pics_erp = $pics_grouped[PictureType::ERPPicture];
    $pics_www = $pics_grouped[PictureType::WWWPicture];

    include('Smarty.class.php');
    $smarty = new Smarty;

    $smarty->assign('slab_id', $slab_id);
    $smarty->assign('frame_id', $frame_id);
    $smarty->assign('slab_frame_id', $slab_frame_id);

    $smarty->assign('stone', $stone);
    $smarty->assign('pics_raw', $pics_raw);
    $smarty->assign('pics_erp', $pics_erp);
    $smarty->assign('pics_www', $pics_www);
    $smarty->assign('error',$error);

    $smarty->assign('script', 'editframepictures');
    $smarty->display('editframepictures.tpl');
 // }