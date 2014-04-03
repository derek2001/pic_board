{include file="header.tpl"}
<script type="text/javascript" src="js/design.js"></script>
<script type="text/javascript" src="js/ajax/jquery-1.4.2.js"></script>
{literal}
    <script>
        var newWind = null;
        $(document).ready(function(){

            $(".test div").click(function(e) {
                var arr_ids = this.id.split('_');
                if (arr_ids[0] > 0 || arr_ids[1] > 0 || arr_ids[2] > 0 || arr_ids[3] > 0 )
                    if(newWind==null || newWind.closed)
                        newWind=window.open('editframepictures.php?slab_frame_id='+arr_ids[0]+'&slab_id='+arr_ids[1]+'&frame_id='+arr_ids[2]+'&stone_id='+arr_ids[3],
                                'editframepictures','width=820,height=540,left=100,top=30,location=no,menubar=no,resizable=yes,scrollbars,toolbar=no');
            });
        });

    </script>
{/literal}
{literal}
    <style>
        em {
            width: 250px;
            height: 50px;
            position: absolute;
            text-align: left;
            font-size: 11px;
            padding: 10px;
            font-style: normal;
            z-index: 20;
            display: none;
            background-color:#FFFFFF;
            border:1px solid red;
        }
    </style>
{/literal}
{assign var="module_name" value="Picture Board"}
{include file="module_header.tpl"}

{assign var="table_width" value="1222"}
{assign var="table_headertitle" value=""}
{include file="table_header.tpl"}

<tr>
    <td align="right" width="55%">
        <div style="float:right;"> &nbsp;&nbsp;&nbsp;&nbsp; </div>
        <div style="width:70px; background-color: #c2f19f; border: 1px solid black; float:right; font-size:11px">DONE?</div>
        <div style="float:right; padding:0 5px 0 5px"> | </div>
        <div style="width:70px; background-color: #c2f19f; border: 1px solid black; float:right; font-size:11px">DONE</div>
        <div style="float:right; padding:0 5px 0 5px"> | </div>
        <div style="width:70px; background-color: #c2f19f; color:white; border: 1px solid black; float:right; font-size:11px">WWW DONE</div>
        <div style="float:right; padding:0 5px 0 5px"> | </div>
        <div style="width:70px; background-color: orange; border: 1px solid black; float:right; font-size:11px">ERP DONE</div>
        <div style="float:right; padding:0 5px 0 5px"> | </div>
        <div style="width:70px; background-color: orange; color:white; border: 1px solid black; float:right; font-size:11px">JPEG DONE</div>
        <div style="float:right; padding:0 5px 0 5px"> | </div>
        <div style="width:70px; background-color: #ffa399; color:white; border: 1px solid black; float:right; font-size:11px">RAW OK</div>
        <div style="float:right; padding:0 5px 0 5px"> | </div>
        <div style="width:70px; background-color: #ffa399; border: 1px solid black; float:right; font-size:11px">NOT DONE</div>
    </td>
</tr>
<tr>
<td colspan="2">
<br>
{counter start=0 print=false assign="cnt" }
<span class="test">
{section name=f loop=$data}
    <span>

    {if $data[f.index_prev].id_frame == $data[f].id_frame
        && $data[f.index_prev].id_slab == $data[f].id_slab
        && ($data[f].slab_board == null || $data[f].slab_board == 0)
        && (!is_array($data[f].history)
        && !is_array($data[f].history[0].id_frame)
        && $data[f].history[0].id_frame.old_sign == '')}
    {else}
        {counter}
        <div class="test" id="{$data[f].id}_{$data[f].id_slab}_{$data[f].id_frame}_{$data[f].id_stone}"
             style="font-size: 11px; float:left; width:40px; margin: 3px;
                     padding: 1px; border: 1px solid black;
             {if $data[f].www_pic.pict1 != '' && $data[f].www_pic.pict2 != '' && $data[f].www_pic.pict3 != ''}
                 background-color: #c2f19f;
             {elseif $data[f].pic_count.cnt_www > 2}
                 background-color: #c2f19f; color: #ffffff;
             {elseif $data[f].pic_count.cnt_erp > 0}
                 background-color: orange;
             {elseif $data[f].pic_count.cnt_jpg > 0}
                 background-color: orange; color: #ffffff;
             {elseif $data[f].pic_count.cnt_raw > 0}
                 background-color: #ffa399; color: #ffffff;
             {else}
                 background-color: #ffa399;
             {/if};
            text-align:center">

            <span class='sign'>
                {$data[f].sign}{if $data[f].pic_count.status ==1 && $data[f].www_pic.pict1 != '' && $data[f].www_pic.pict2 != '' && $data[f].www_pic.pict3 != ''}?{/if}
            </span>

        </div>
        </span>
    {/if}
	{if $cnt % 104 == 0 }<div style="clear:both; display:block"><br /></div>{/if}
{/section}
</span>
    </td>
</tr>

{include file="table_footer.tpl"}
<br /><br />
{include file="foot.tpl"}