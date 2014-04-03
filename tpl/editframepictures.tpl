{include file="header_no_user.tpl"}
<script type="text/javascript" src="js/design.js"></script>
<script type="text/javascript" src="js/ajax/jquery-1.6.2.min.js"></script>
<script type="text/javascript">
{literal}
    //ADDED START
    //window.onunload = refreshParent;
    function refreshParent() {
        window.opener.location.reload();
    }
    //ADDED END
{/literal}
</script>
<script language="JavaScript">
{literal}
    $(document).ready(function() {
        $("a.delete").click(function(e) {
            if (!confirm("Are you sure you want to delete this?")){
                return false;
            }
        });
        // validates counts of checked images by type
        /*
        $('#form_editframepictures').submit(function(e) {
            var erpFullCount = $('input[type="checkbox"][id*="status_1_0"]:checked').length;
            var erpMediumCount = $('input[type="checkbox"][id*="status_1_1"]:checked').length;
            var erpMacroCount = $('input[type="checkbox"][id*="status_1_2"]:checked').length;
            var wwwFullCount = $('input[type="checkbox"][id*="status_2_0"]:checked').length;
            var wwwMediumCount = $('input[type="checkbox"][id*="status_2_1"]:checked').length;
            var wwwMacroCount = $('input[type="checkbox"][id*="status_2_2"]:checked').length;

            if (erpFullCount > 1 || erpMediumCount > 1 || erpMacroCount > 1)
            {
                var errorMessage = "You can only check one erp image of the same type (full or medium or macro)";
                alert (errorMessage);
                return false;
            }
            if (wwwFullCount > 1 || wwwMediumCount > 1 || wwwMacroCount > 1)
            {
                var errorMessage = "You can only check one www image of the same type (full or medium or macro)";
                alert (errorMessage);
                return false;
            }

            return true;
        });
        */
    });
{/literal}
</script>

<script language="JavaScript">
{literal}
    function openPopupImage(filePath)
    {
        newWind=window.open("" + filePath,
                'imagepopup','width=820,height=540,left=150,top=80,location=no,menubar=no,resizable=yes,scrollbars,toolbar=no');
    }
{/literal}
</script>

<style type="text/css">
{literal}
#imagePreview {
    position:absolute;
    border:1px solid #ccc;
    background:#333;
    padding:2px;
    display:none;
    color:#fff;
}

img {
    border:none;
}

p {
    clear:both;
    margin:0;
    padding:.5em 0;
}

a.disabled
{
    color: #808080;
    cursor: default !important;;
}

{/literal}
</style>

<!-- frame information -->
{assign var="table_headertitle" value="Stone information"}
{include file="search_header.tpl"}
<tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
    <td nowrap>ID:</td><td>{$stone.id}</td>
</tr>
<tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
    <td nowrap>NET ID</td><td>&nbsp;</td>
</tr>
<tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
    <td nowrap>Name</td><td>{$stone.name}</td>
</tr>
<tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
    <td nowrap>Location:</td><td>{$stone.location_name}</td>
</tr>
<tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
    <td nowrap>Frame</td><td>{$stone.frame_sign}</td>
</tr>
{include file="search_footer.tpl"}

<form id="form_editframepictures" enctype="multipart/form-data" method="POST" action="editframepictures.php?slab_frame_id={$slab_frame_id}&slab_id={$slab_id}&frame_id={$frame_id}&stone_id={$stone_id}">
    <!-- used for post backs -->
    <input type="hidden" name="slab_id" value="{$slab_id}" />
    <input type="hidden" name="frame_id" value="{$frame_id}" />
    <input type="hidden" name="slab_frame_id" value="{$slab_frame_id}" />
    <input type="hidden" name="stone_id" value="{$stone_id}" />

    <!-- needs more photos information -->
    <!--
    {assign var="table_headertitle" value="More pictures"}
    {include file="search_header.tpl"}
    <tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
        <td nowrap>Need to take more pictures if new slab</td>
        <td>
            <input type="checkbox" name="test_chb" {if $need_pics_status == 1} checked="checked" {/if} {if $need_more_pics_disabled == true} disabled="disabled" {/if}  />
        </td>
    </tr>
    {include file="search_footer.tpl"}
    -->

    <!-- picture information -->
    {assign var="table_headertitle" value="Picture information"}
    {include file="search_header.tpl"}
    <tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
        <td colspan="2">{if $error<>''}<br><b><font color="red">{$error}</font></b><br>{/if}</td>
    </tr>
    <tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
        <td nowrap>RAW picture:</td>
        <td>
            {if is_array($pics_raw) && count($pics_raw)>0}
            <table width="100%">
            {section name=i loop=$pics_raw}
                <tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
                    <td width="100%">
                        <input type="hidden" name="id[]" value="{$pics_raw[i].id}" />
                        <input type="file" {if $pics_raw[i].id != 0} disabled="disabled" {/if} name="picture_to_upload_{$pics_raw[i].type}[]" id="{$pics_raw[i].id_slab_frame}_{$pics_raw[i].type}_{$pics_raw[i].size}" />

                        &nbsp;
                        <input type="checkbox" disabled="disabled" {if $pics_raw[i].id != 0} checked="checked" {/if} />
                        &nbsp;
                        <!--
                        <input type="checkbox" value="{$pics_raw[i].id}" id="{$pics_raw[i].id}_status_{$pics_raw[i].type}_{$pics_raw[i].size}" {if $pics_raw[i].status == 1} checked="checked" {/if} name="raw_picture_status[]" {if $pics_raw[i].id == 0} disabled="disabled" {/if} />
                        -->
                        {if $pics_raw[i].id != 0}
                            <a  class="imagePreview"  href="download.php?file={$pics_raw[i].full_path}">{$pics_raw[i].label}</a>
                        {else}
                            <a href="#" class="disabled">{$pics_raw[i].label}</a>
                        {/if}
                    </td>
                    <td>
                        {if $pics_raw[i].id != 0}
                            <a class="delete" href="editframepictures.php?id={$pics_raw[i].id}&slab_frame_id={$pics_raw[i].id_slab_frame}&slab_id={$pics_raw[i].id_slab}&frame_id={$pics_raw[i].id_frame}&stone_id={$pics_raw[i].id_stone}&act=del">Delete</a>
                        {else}
                            <a href="#" class="disabled">Delete</a>
                        {/if}
                    </td>
                </tr>
            {/section}
            </table>
            {/if}
        </td>
    </tr>
    <tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
        <td nowrap>JPEG picture:</td>
        <td>
            {if is_array($pics_jpeg) && count($pics_jpeg)>0}
                <table width="100%">
                    {section name=i loop=$pics_jpeg}
                        <tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
                            <td width="100%">
                                <input type="hidden" name="id[]" value="{$pics_jpeg[i].id}" />
                                <input type="file" {if $pics_jpeg[i].id != 0} disabled="disabled" {/if} name="picture_to_upload_{$pics_jpeg[i].type}[]" id="{$pics_jpeg[i].id_slab_frame}_{$pics_jpeg[i].type}_{$pics_jpeg[i].size}" />
                                &nbsp;
                                <input type="checkbox" disabled="disabled" {if $pics_jpeg[i].id != 0} checked="checked" {/if} />
                                &nbsp;
                                <!--
                                <input type="checkbox" value="{$pics_jpeg[i].id}" id="{$pics_jpeg[i].id}_status_{$pics_jpeg[i].type}_{$pics_jpeg[i].size}" {if $pics_jpeg[i].status == 1} checked="checked" {/if} name="raw_picture_status[]" {if $pics_jpeg[i].id == 0} disabled="disabled" {/if} />
                                -->
                                {if $pics_jpeg[i].id != 0}
                                    <a  class="imagePreview" href="#" onclick="openPopupImage('{$pics_jpeg[i].full_path}');">{$pics_jpeg[i].label}</a>
                                {else}
                                    <a href="#" class="disabled">{$pics_jpeg[i].label}</a>
                                {/if}
                            </td>
                            <td>
                                {if $pics_jpeg[i].id != 0}
                                    <a class="delete" href="editframepictures.php?id={$pics_jpeg[i].id}&slab_frame_id={$pics_jpeg[i].id_slab_frame}&slab_id={$pics_jpeg[i].id_slab}&frame_id={$pics_jpeg[i].id_frame}&stone_id={$pics_jpeg[i].id_stone}&act=del">Delete</a>
                                {else}
                                    <a href="#" class="disabled">Delete</a>
                                {/if}
                            </td>
                        </tr>
                    {/section}
                </table>
            {/if}
        </td>
    </tr>
    <tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
        <td nowrap>ERP picture</td>
        <td>
            {if is_array($pics_erp) && count($pics_erp)>0}
            <table width="100%">
            {section name=i loop=$pics_erp}
                    <tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
                        <td width="100%">
                            <input type="hidden" name="id[]" value="{$pics_erp[i].id}" />
                            <input type="file" {if $pics_erp[i].id != 0} disabled="disabled" {/if} name="picture_to_upload_{$pics_erp[i].type}[]"  id="{$pics_erp[i].id_slab_frame}_{$pics_erp[i].type}_{$pics_erp[i].size}" />
                            &nbsp;
                            <input type="checkbox" disabled="disabled" {if $pics_erp[i].id != 0} checked="checked" {/if} />
                            &nbsp;
                            <!--
                            <input type="checkbox" value="{$pics_erp[i].id}" id="{$pics_erp[i].id}_status_{$pics_erp[i].type}_{$pics_erp[i].size}" {if $pics_erp[i].status == 1} checked="checked" {/if} name="erp_picture_status[]" {if $pics_erp[i].id == 0} disabled="disabled" {/if} />
                            -->
                            {if $pics_erp[i].id != 0}
                                <a  class="imagePreview" href="#" onclick="openPopupImage('{$pics_erp[i].full_path}');">{$pics_erp[i].label}</a>
                            {else}
                                <a href="#" class="disabled">{$pics_erp[i].label}</a>
                            {/if}
                        </td>
                        <td>
                            {if $pics_erp[i].id != 0}
                                <a class="delete" href="editframepictures.php?id={$pics_erp[i].id}&slab_frame_id={$pics_erp[i].id_slab_frame}&slab_id={$pics_erp[i].id_slab}&frame_id={$pics_erp[i].id_frame}&stone_id={$pics_erp[i].id_stone}&act=del">Delete</a>
                            {else}
                                <a href="#" class="disabled">Delete</a>
                            {/if}
                        </td>
                    </tr>
            {/section}
            </table>
            {/if}
        </td>
    </tr>
    <tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
        <td nowrap>WWW picture</td>
        <td>
            {if is_array($pics_www) && count($pics_www)>0}
            <table width="100%">
            {section name=i loop=$pics_www}
                <tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
                    <td width="100%">
                        <input type="hidden" name="id[]" value="{$pics_www[i].id}" />
                        <input type="file" {if $pics_www[i].id != 0} disabled="disabled" {/if} name="picture_to_upload_{$pics_www[i].type}_{$pics_www[i].size}[]" id="{$pics_www[i].id_slab_frame}_{$pics_www[i].type}_{$pics_www[i].size}" />
                        &nbsp;
                        <input type="checkbox" disabled="disabled" {if $pics_www[i].id != 0} checked="checked" {/if} />
                        &nbsp;
                        <!--
                        <input type="checkbox" value="{$pics_www[i].id}" id="{$pics_www[i].id}_status_{$pics_www[i].type}_{$pics_www[i].size}" {if $pics_www[i].status == 1} checked="checked" {/if} name="www_picture_status[]" {if $pics_www[i].id == 0} disabled="disabled" {/if} />
                        -->
                        {if $pics_www[i].id != 0}
                            <a  class="imagePreview" href="#" onclick="openPopupImage('{$pics_www[i].full_path}');">{$pics_www[i].label}</a>
                        {else}
                            <a href="#" class="disabled">{$pics_www[i].label}</a>
                        {/if}
                    </td>
                    <td>
                        {if $pics_www[i].id != 0}
                            <a class="delete" href="editframepictures.php?id={$pics_www[i].id}&slab_frame_id={$pics_www[i].id_slab_frame}&slab_id={$pics_www[i].id_slab}&frame_id={$pics_www[i].id_frame}&stone_id={$pics_www[i].id_stone}&act=del">Delete</a>
                        {else}
                            <a href="#" class="disabled">Delete</a>
                        {/if}
                    </td>
                </tr>
            {/section}
            </table>
            {/if}
        </td>
    </tr>
    <tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
        <td>&nbsp;</td>
        <td>
            <span>Need to take more pictures if new slab</span>&nbsp;&nbsp;
            <input type="checkbox" name="need_pics_status" {if $need_pics_status_val == 1} value="1" checked="checked" {/if} {if $need_more_pics_disabled == true} disabled="disabled"  {/if}  />
        </td>
    </tr>
    <tr >
        <td colspan="2">
            <input type="button" class="BUTTON_CANCEL" value="Cancel" onclick="window.close()"/>&nbsp;&nbsp;
            <input type="submit" class="BUTTON_OK" value="Save" onclick="refreshParent()"/>
        </td>
    </tr>
    {include file="search_footer.tpl"}
</form>