{include file="header_no_user.tpl"}
<script type="text/javascript" src="js/design.js"></script>
<script type="text/javascript" src="js/ajax/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="__STONE_PICS/main.js"></script>

<script language="JavaScript">
{literal}
    $(document).ready(function() {
        $("a.delete").click(function(e) {
            if (!confirm("Are you sure you want to delete this?")){
                return false;
            }
        });
    });
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

    <!-- picture information -->
    <form id="form_editframepictures" enctype="multipart/form-data" method="POST" action="editframepictures.php">
        <!-- used for post backs -->
        <input type="hidden" name="slab_id" value="{$slab_id}" />
        <input type="hidden" name="frame_id" value="{$frame_id}" />
        <input type="hidden" name="slab_frame_id" value="{$slab_frame_id}" />

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
                        <input type="checkbox" value="{$pics_raw[i].id}" {if $pics_raw[i].status == 1} checked="checked" {/if} name="raw_picture_status[]" {if $pics_raw[i].id_slab_frame == 0} disabled="disabled" {/if} />
                        {if $pics_raw[i].stone_id != 0}
                            <a  class="imagePreview"  href="{$pics_raw[i].full_path}" rel="{$pics_raw[i].full_path_thumbnail}">{$pics_raw[i].label}</a>
                        {else}
                            <a href="#" class="disabled">{$pics_raw[i].label}</a>
                        {/if}
                    </td>
                    <td>
                        {if $pics_raw[i].stone_id != 0}
                            <a class="delete" href="editframepictures.php?id={$pics_raw[i].id}&slab_frame_id={$pics_raw[i].id_slab_frame}&slab_id={$pics_raw[i].id_slab}&frame_id={$pics_raw[i].id_frame}&act=del">Delete</a>
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
            <table>
            {section name=i loop=$pics_erp}
                    <tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
                        <td>
                            <input type="hidden" name="id[]" value="{$pics_erp[i].id}" />
                            <input type="file" {if $pics_erp[i].id != 0} disabled="disabled" {/if} name="picture_to_upload_{$pics_erp[i].type}_{$pics_erp[i].size}[]"  id="{$pics_erp[i].id_slab_frame}_{$pics_erp[i].type}_{$pics_erp[i].size}" />
                            <input type="checkbox" value="{$pics_erp[i].id}" {if $pics_erp[i].status == 1} checked="checked" {/if} name="erp_picture_status[]" {if $pics_erp[i].id_slab_frame == 0} disabled="disabled" {/if} />
                            {if $pics_erp[i].id_slab_frame != 0}
                                <a  class="imagePreview"  href="{$pics_erp[i].full_path}" rel="{$pics_erp[i].full_path_thumbnail}">{$pics_erp[i].label}</a>
                            {else}
                                <a href="#" class="disabled">{$pics_erp[i].label}</a>
                            {/if}
                        </td>
                        <td>
                            {if $pics_erp[i].id_slab_frame != 0}
                                <a class="delete" href="editframepictures.php?id={$pics_erp[i].id}&slab_frame_id={$pics_erp[i].id_slab_frame}&slab_id={$pics_erp[i].id_slab}&frame_id={$pics_erp[i].id_frame}&act=del">Delete</a>
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
            <table>
            {section name=i loop=$pics_www}
                <tr class="cell_reccolor_blue_01{cycle name="parity" values="a,b"}">
                    <td>
                        <input type="hidden" name="id[]" value="{$pics_www[i].id}" />
                        <input type="file" {if $pics_www[i].id_slab_frame != 0} disabled="disabled" {/if} name="picture_to_upload_{$pics_www[i].type}_{$pics_www[i].size}[]" id="{$pics_www[i].id_slab_frame}_{$pics_www[i].type}_{$pics_www[i].size}" />
                        <input type="checkbox" value="{$pics_www[i].id}" {if $pics_www[i].status == 1} checked="checked" {/if} name="www_picture_status[]" {if $pics_www[i].id_slab_frame == 0} disabled="disabled" {/if} />
                        {if $pics_www[i].id_slab_frame != 0}
                            <a  class="imagePreview"  href="{$pics_www[i].full_path}" rel="{$pics_www[i].full_path_thumbnail}">{$pics_www[i].label}</a>
                        {else}
                            <a href="#" class="disabled">{$pics_www[i].label}</a>
                        {/if}
                    </td>
                    <td>
                        {if $pics_www[i].id_slab_frame != 0}
                            <a class="delete" href="editframepictures.php?id={$pics_www[i].id}&slab_frame_id={$pics_www[i].id_slab_frame}&slab_id={$pics_www[i].id_slab}&frame_id={$pics_www[i].id_frame}&act=del">Delete</a>
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
    <tr >
        <td colspan="2">
            <input type="button" class="BUTTON_CANCEL" value="Cancel" onclick="window.close()"/>&nbsp;&nbsp;
            <input type="submit" class="BUTTON_OK" value="Save" />
        </td>
    </tr>
    {include file="search_footer.tpl"}
</form>