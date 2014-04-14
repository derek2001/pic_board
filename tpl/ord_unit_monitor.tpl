{include file="header_no_user.tpl"}
<script type="text/javascript" src="js/design.js"></script>
<script type="text/javascript" src="js/ajax/jquery-1.6.2.min.js"></script>
<meta http-equiv="refresh" content="5">
{dhtml_calendar_init src='js/jscalendar/calendar.js' setup_src='js/jscalendar/calendar-setup.js' lang='js/jscalendar/lang/calendar-en.js' css='js/jscalendar/calendar-system.css'}
{if $error<>''}<br><b><font color="red">{$error}</font></b>{/if}
{assign var="module_name" value="Unit Procedure Monitor"}
{include file="module_header.tpl"}
<br>

{assign var="table_width" value="1650"}
{assign var="table_headertitle" value="Status"}
{include file="table_header.tpl"}
{if count($order)>0}
    <table width="100%" style="font-size: 105">
    <tr class="cell_reccolor_neutral_01">
        <td width="5%" style="font-size: 30">Order</a></td>
        <td width="5%" style="font-size: 30">Unit</a></td>
        <td width="28%" style="font-size: 30">Template</a></td>
        <td width="28%" style="font-size: 30">Production</a></td>
        <td width="28%" style="font-size: 30">Installation</a></td>
        <td width="6%" style="font-size: 30">Status</a></td>
    </tr>
    {assign var="columns" value="0,1,2,3,4,5"}
    {section name=dat loop=$order}
        {$test = $order[dat].id_order}
        {assign var="_i_" value=$smarty.section.dat.index}
        {if $_i_ is even}{assign var="_j_" value="1"}{else}{assign var="_j_" value="0"}{/if}
        {if $main[idx].type != 0}
            {if $main[idx].type == 3}
                {assign var="url_suffix" value="&tid=3"}
            {else}
                {assign var="url_suffix" value="&tid=2"}
            {/if}
        {else}
            {assign var="url_suffix" value="&tid=0"}
        {/if}
        <tr align="right" valign="top" onMouseOver="RowOver({$_i_})" onMouseOut="RowOut({$_i_})">
            <td align="left" style="font-size: 15" class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="5%" height="28">
                <b>{$order[dat].id_location|location}&nbsp;{if $test == $id }{$img.id}{$order[dat].id_order}</b>{else}{''}{/if}</td>
            <td style="font-size: 15" class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="5%" height="28">
                <b>{$order[dat].id_ord_unit}</b></td>
            <td style="font-size: 15" class="row{$_j_}{cycle name="parity" values="1,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="28%" height="28">
                {if $order[dat].c_status == 0}<b>{'Template not start yet.'}</b><br>
                {elseif $order[dat].c_status == 1}<b>{'In process '}{$order[dat].c_name}<br>{'START '}{$order[dat].c_start_time}</b>
                {elseif $order[dat].c_status == 2}<b>{'Finished '}{$order[dat].c_name}</b>
                    <br><b>{'START '}{$order[dat].c_start_time}
                    {'- END '}{$order[dat].c_fin_time}</b>
                {/if}
            </td>
            <td style="font-size: 15" class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="28%" height="28">
                {if $order[dat].p_status == 0}<b>{'Production not start yet.'}</b><br>
                {elseif $order[dat].p_status == 1}<b>{'In process '}{$order[dat].p_name}<br>{' START '}{$order[dat].p_start_time}</b>
                {elseif $order[dat].p_status == 2}<b>{'Finished '}{$order[dat].p_name}</b>
                    <br><b>{' START '}{$order[dat].p_start_time}
                    {'- END '}{$order[dat].p_fin_time}</b>
                {/if}
            </td>
            <td style="font-size: 15" class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="28%" height="28">
                {if $order[dat].i_status == 0}<b>{'Installation not start yet.'}</b><br>
                {elseif $order[dat].i_status == 1}<b>{'In process '}{$order[dat].i_name}<br><b>{' START '}{$order[dat].i_start_time}}</b>
                {elseif $order[dat].i_status == 2}<b>{'Finished '}{$order[dat].i_name}}</b>
                    <br><b>{' START '}{$order[dat].i_start_time}}
                           {'- END '}{$order[dat].i_fin_time}}</b>
                {/if}
            </td>
            <td style="font-size: 15" align="center" class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="6%" height="28">
                {if $order[dat].u_status==1 }<b> Standard </b>
                {else}<b> Obsolete </b>
                {/if} </td>
        </tr>
        {$id = $order[dat].id_order}
    {/section}
    {include file="table_footer.tpl"}
    </table>
{else}
    <b><font color="red">No statuses defined.</font></b>
{/if}
<br>
<br>