<?
function decodeValue(&$arr,$value)
{
	 $convertTab = array('id_slab'=>'Slab ID', 'id_frame'=>'Frame','slab_nbr'=>'Number of slabs');
	 $aux = explode ( '#:#', $value);
     if (is_array($aux))
      foreach($aux as $v)
      {
      	$pom1 = explode('###',$v);
      	$pom2 = explode(':::',$pom1[1]);
      	if (is_array($pom1) && is_array($pom2) && trim($pom1[0])!='')
      		$arr[$pom1[0]] = array(
				'name'=> isset($convertTab[$pom1[0]])?$convertTab[$pom1[0]]:ucfirst($pom1[0]),
				'old' => $pom2[0],
				'new' => $pom2[1] 
			);
      }
}

function decodeSlab(&$arr,$value)
{
	 $convertTab = array('id_slab'=>'Slab ID', 'id_frame'=>'Frame','slab_nbr'=>'Number of slabs');
	 $aux = explode ( '#:#', $value);
     if (is_array($aux))
      foreach($aux as $v)
      {
      	$pom1 = explode('###',$v);
      	$pom2 = explode(':::',$pom1[1]);
      	if (is_array($pom1) && trim($pom1[0])=='slab')
      	  if (is_array($pom2) && trim($pom2[0])=='id_slab')
      	    if (!isset($arr[$pom2[0]]))
      	    {
	      		$arr[$pom2[0]] = array(
					'name'=> isset($convertTab[$pom2[0]])?$convertTab[$pom2[0]]:ucfirst($pom2[0]),
					'old' => $pom2[1],
					'new' => $pom2[1] 
				);
				break;
      	    }
      }
}

include_once("config.php");

include('Smarty.class.php');
$smarty = new Smarty;
        //H E A D E R - MENU  --------------------------------------------------
        $smarty->assign('user_profile',$_SESSION['user']->getUserName());
        global $css_color,$date_format;
        $smarty->assign('css_color',$css_color);
        $smarty->assign('time',date($date_format,time()));
        $smarty->assign('menulink',$_SESSION['user']->getMenuLink());
        //----------------------------------------------------------------------


if(isset($_POST['update']) && isset($_FILES['updatelist'])) {
	$lines = file($_FILES['updatelist']['tmp_name']);

	// Loop through our array, show HTML source as HTML source; and line numbers too.
	foreach ($lines as $line_num => $line) {
		$temp = explode(',', $line);
		$param = explode('/', $temp[3]);
		$_SESSION["user"]->db->select("exec [dbo].[update_slab_board] ".$param[0].",".$param[1].",".$param[2].",".$param[3]);
	}
	unset($_POST); unset($_FILES);
	header("Location: picture_board.php");
}
		
		
if(isset($_GET['cleardup']) && $_GET['cleardup']==1) {
	$_SESSION["user"]->db->select("ALTER TABLE slab_frame_duplicate ADD id int IDENTITY(1,1)");	
	$_SESSION["user"]->db->select("WITH Dublicates_CTE(id_slab_frame, id_slab, id_frame, id)
									AS
									(
									SELECT id_slab_frame, id_slab, id_frame, Min(id) id
									FROM slab_frame_duplicate
									GROUP BY id_slab_frame, id_slab, id_frame
									HAVING Count(*) > 1
									)
									DELETE FROM slab_frame_duplicate
									WHERE id IN (
									SELECT slab_frame_duplicate.id
									FROM slab_frame_duplicate
									INNER JOIN Dublicates_CTE
									ON slab_frame_duplicate.id_slab_frame = Dublicates_CTE.id_slab_frame
									AND slab_frame_duplicate.id_slab = Dublicates_CTE.id_slab
									AND slab_frame_duplicate.id_frame = Dublicates_CTE.id_frame
									AND slab_frame_duplicate.id <> Dublicates_CTE.id
									)");	
	$_SESSION["user"]->db->select("ALTER TABLE slab_frame_duplicate DROP COLUMN id");	
	header("Location: picture_board.php");
}
if(isset($_GET['cleardup']) && $_GET['cleardup']==2) {
	$_SESSION["user"]->db->select("delete from slab_frame_duplicate");
	$_SESSION["user"]->db->select("update slab_frame set slab_board = null, slab_board_date = null");
	header("Location: picture_board.php");
}


if(isset($_GET['print'])) {
	$print = explode(",",$_GET['print']);
	echo "<table border=1 cellpadding=2 cellspacing=2>";
	for($x = 0; $x < count($print)-1; $x++) {
		if($x == 0 || $x % 3 == 0) echo "\n<tr>\n";
		echo "\n<td nowrap>".trim($print[$x])."</td>\n";
		if($x != 0)
			if($x+1 % 3 == 0 || (count($print)-2) == $x) echo "\n</tr>\n";
	}
	echo "</table>";
	die();
}

if(isset($_GET['remove']) && isset($_GET['sf'])) {
	$_SESSION["user"]->db->select("update slab_frame set slab_board = null, slab_board_date = null where id=".$_GET['sf']);
	$_SESSION["user"]->db->select("delete from slab_frame_duplicate where id_slab=".$_GET['s']." and id_frame=".$_GET['f']);
	exit();
}
if(isset($_GET['reset']) && isset($_GET['sf'])) {
	$_SESSION["user"]->db->select("update slab_frame set slab_board_date = current_timestamp where id=".$_GET['sf']);
	exit();
}

$loc = $_SESSION["user"]->getLocationProfile();

$_SESSION["user"]->db->select("select sf.id,sf.id_slab,sf.id_frame,slab_board,sf.slab_nbr,f.sign, s.id_stone
								from slab_frame sf
								inner join frame f on f.id=sf.id_frame
								inner join slab s on s.id=sf.id_slab
								left join slab_frame_duplicate sfd on sfd.id_slab=sf.id_slab and sfd.id_frame=sf.id_frame
								where f.id_location=".$loc['id']."
								and slab_board = 1
								and sf.slab_nbr>=0
								and s.deleted = 0
								and s.is_unknown = 0
								and sf.id_frame NOT IN (1283, 1364, 1474, 627)
								union
								select sf.id,sf.id_slab,sf.id_frame,slab_board,sf.slab_nbr,f.sign, s.id_stone
								from slab_frame sf
								inner join frame f on f.id=sf.id_frame
								inner join slab s on s.id=sf.id_slab
								left join slab_frame_duplicate sfd on sfd.id_slab=sf.id_slab and sfd.id_frame=sf.id_frame
								where f.id_location=".$loc['id']."
								and (slab_board is null or slab_board = 0)
								and sf.slab_nbr>0
								and s.deleted = 0
								and s.is_unknown = 0
								and sf.id_frame NOT IN (1283, 1364, 1474, 627)
								union
								select null,sfd.id_slab,sfd.id_frame, null, 0, f.sign, s.id_stone
								from slab_frame_duplicate sfd
								left join slab_frame sf on sfd.id_slab=sf.id_slab and sfd.id_frame=sf.id_frame
								inner join frame f on f.id=sfd.id_frame
								inner join slab s on s.id=sfd.id_slab
								where f.id_location=".$loc['id']."
								and s.deleted = 0
								and s.is_unknown = 0
								and sf.id_slab is null
								and sfd.id_frame NOT IN (1283, 1364, 1474, 627)
								order by f.sign, slab_board desc, slab_nbr desc");

$samples = $_SESSION["user"]->db->fetchAllArrays();
							
$sql = "SELECT
			id_slab_frame, id_slab, id_frame, count(*) as cnt
		FROM
			slab_frame_duplicate
		GROUP BY
			id_slab_frame, id_slab, id_frame
		HAVING
			(COUNT(id_slab_frame) > 1) or (COUNT(id_slab) > 1) or (COUNT(id_frame) > 1) ";
			
$_SESSION["user"]->db->select($sql);
$dup = $_SESSION["user"]->db->fetchAllArrays();

$duplicates = array();
if($_SESSION["user"]->db->numrows() > 0)
foreach($dup as $dups) {

	$_SESSION["user"]->db->select("select st_name, cat_name, frame,
	                                ".$dups['cnt']." as cnt from
	                                v_stonesearch
	                                where id_frame=".$dups['id_frame']."
	                                and id_slab=".$dups['id_slab']."
	                                and id_slab_frame=".$dups['id_slab_frame']."");
	$tmp = $_SESSION["user"]->db->fetchArray();

	if($_SESSION["user"]->db->numRows() > 0)
		$duplicates[] = $tmp;
}

$raw_count = array();
foreach($samples as $k=>$sample) {
    if(strcmp($sample['id'], '') == 0)
        $id = 0;
    else
        $id = $sample['id'];
    $_SESSION["user"]->db->select("select sl.id_slab_frame as id, sl.id_slab, sl.id_frame,
            (select count(slc.type) from [dbo].[stone_image] slc where slc.type=0 and sl.id_slab_frame = slc.id_slab_frame and sl.id_slab = slc.id_slab and sl.id_frame = slc.id_frame) cnt_raw,
            (select count(sla.type) from [dbo].[stone_image] sla where sla.type=2 and sl.id_slab_frame = sla.id_slab_frame and sl.id_slab = sla.id_slab and sl.id_frame = sla.id_frame) cnt_erp,
            (select count(slb.type) from [dbo].[stone_image] slb where slb.type=3 and sl.id_slab_frame = slb.id_slab_frame and sl.id_slab = slb.id_slab and sl.id_frame = slb.id_frame) cnt_www,
            (select count(sld.type) from [dbo].[stone_image] sld where sld.type=1 and sl.id_slab_frame = sld.id_slab_frame and sl.id_slab = sld.id_slab and sl.id_frame = sld.id_frame) cnt_jpg,
            (select distinct status from [dbo].[stone_image] sle where sl.id_slab_frame = sle.id_slab_frame and sl.id_slab = sle.id_slab and sl.id_frame = sle.id_frame) status
            from [dbo].[stone_image] sl
            where sl.id_slab_frame = ".$id."
            and sl.id_slab = ".$sample['id_slab']."
            and sl.id_frame = ".$sample['id_frame']."
            group by sl.id_slab_frame, sl.id_slab, sl.id_frame
            ");

    $cnt = $_SESSION["user"]->db->fetchArray();
    if($_SESSION["user"]->db->numrows() > 0)
    {
        $samples[$k]['pic_count'] = $cnt;
    }

    $_SESSION["user"]->db->select("select pict1, pict2, pict3 from [dbo].[stone_drawing] sd
        where sd.id_stone = ".$sample['id_stone']."
        and sd.id_slab = ".$sample['id_slab']."
        ");

    $www = $_SESSION["user"]->db->fetchArray();
    if($_SESSION["user"]->db->numrows() > 0)
    {
        $samples[$k]['www_pic'] = $www;
    }
}

$smarty->assign('duplicates',$duplicates);
$smarty->assign('data',$samples);

$smarty->display('picture_board.tpl');

?>
