{include file="header.tpl"}
<script type="text/javascript" src="js/design.js"></script>
<script type="text/javascript" src="js/ajax/jquery-1.6.2.min.js"></script>
{dhtml_calendar_init src='js/jscalendar/calendar.js' setup_src='js/jscalendar/calendar-setup.js' lang='js/jscalendar/lang/calendar-en.js' css='js/jscalendar/calendar-system.css'}
{if $error<>''}<br><b><font color="red">{$error}</font></b>{/if}
{literal}
<script language="javascript">
    function clearForm() {
        var search = document.search;
        search.keyword.value = '';
        search.pro_from.value = '';
        search.pro_to.value = '';
        search.cut_from.value = '';
        search.cut_to.value = '';
        search.ins_from.value = '';
        search.ins_to.value = '';
        search.ord.value = '';
        search.unt.value = '';
        search.operator.value = '';
    }
</script>
{/literal}
{assign var="module_name" value="Unit Procedure Monitor"}
{include file="module_header.tpl"}
<br>
<table width="100%" border="0"><tr>
        <td valign="top">
            {assign var="table_width" value="452"}
            {assign var="table_headertitle" value="Search:"}
            {include file="search_header.tpl"}
            <form action="ord_unit_status.php" method="post" name="search" id="search-form">

                <tr class="cell_reccolor_blue_01a">
                    <td nowrap>Keyword:</td>
                    <td><input type="text" name="keyword" value="{$search.keyword}" size="30"></td>
                </tr>
                <tr class="cell_reccolor_blue_01b">
                    <td nowrap>Order Number:</td>
                    <td>
                        <input type="text" name="ord" value="{$search.ord}" size="30">
                    </td>
                </tr>


                <tr class="cell_reccolor_blue_01a">
                    <td nowrap>Unit Number:</td>
                    <td>
                        <input type="text" name="unt" value="{$search.unt}" size="30">
                    </td>
                </tr>
                <tr class="cell_reccolor_blue_01b">
                    <td nowrap>Operator:</td>
                    <td>
                        <input type="text" name="operator" value="{$search.operator}" size="30">
                    </td>
                </tr>
                <tr class="cell_reccolor_blue_01a">
                    <td nowrap>Date of Cutting:</td>
                    <td>
                        <table cellspacing="0" cellpadding="0">
                            <tr class="cell_reccolor_blue_01b">
                                <td width="31">From:</td>
                                <td width="90">
                                    <input type="text" size="10" maxlength="10" name="cut_from" id="cut_from" value="{$search.cut_from}">
                                </td>
                                <td>To:</td>
                                <td width="90">
                                    <input type="text" size="10" maxlength="10" name="cut_to" id="cut_to" value="{$search.cut_to}">
                                </td>
                                <td>{dhtml_calendar_multi inputField1="cut_from" inputField2="cut_to" button="calendar_multi_cut_from" align="T1" multiple="MA" showOthers=true ifFormat="%m/%d/%Y"}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="cell_reccolor_blue_01b">
                    <td nowrap>Date of Production:</td>
                    <td>
                        <table cellspacing="0" cellpadding="0">
                            <tr class="cell_reccolor_blue_01b">
                                <td width="31">From:</td>
                                <td width="90">
                                    <input type="text" size="10" maxlength="10" name="pro_from" id="pro_from" value="{$search.pro_from}">
                                </td>
                                <td>To:</td>
                                <td width="90">
                                    <input type="text" size="10" maxlength="10" name="pro_to" id="pro_to" value="{$search.pro_to}">
                                </td>
                                <td>{dhtml_calendar_multi inputField1="pro_from" inputField2="pro_to" button="calendar_multi_pro_from" align="T1" multiple="MA" showOthers=true ifFormat="%m/%d/%Y"}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="cell_reccolor_blue_01a">
                    <td nowrap>Date of Installation:</td>
                    <td>
                        <table cellspacing="0" cellpadding="0">
                            <tr class="cell_reccolor_blue_01b">
                                <td width="31">From:</td>
                                <td width="90">
                                    <input type="text" size="10" maxlength="10" name="ins_from" id="ins_from" value="{$search.ins_from}">
                                </td>
                                <td>To:</td>
                                <td width="90">
                                    <input type="text" size="10" maxlength="10" name="ins_to" id="ins_to" value="{$search.ins_to}">
                                </td>
                                <td>{dhtml_calendar_multi inputField1="ins_from" inputField2="ins_to" button="calendar_multi_ins_from" align="T1" multiple="MA" showOthers=true ifFormat="%m/%d/%Y"}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="cell_reccolor_grey_01b">
                    <td><input type="button" value="Clear" class="BUTTON_CANCEL" onclick="clearForm();"></td>
                    <td align="right"><input type="submit" name="submit" value="Search" class="BUTTON_OK"></td>
                </tr>
            </form>
            {include file="search_footer.tpl"}

        </td>

        <td valign="top">


            {assign var="table_width" value="150"}
            {assign var="table_headertitle" value="Operations:"}
            {include file="search_header.tpl"}
    <tr class="cell_reccolor_grey_01a">
        <td width="50%">
        <td valign="top"><a href="#" onClick="window.open('print_worker.php','print_worker','toolbar=1,menu=1,scrollbars=1,resizable=1');"><img src="gfx/printer.gif" border="0"></a></td>
    </tr>
    {include file="search_footer.tpl"}

    </td>

    </tr>
</table>
<br>

<!--Paging-->
<center>{$paging}</center>
<!-- Naglowek-->

{assign var="table_width" value="1100"}
{assign var="table_headertitle" value="Status"}
{include file="table_header.tpl"}
{if count($order)>0}
    <tr class="cell_reccolor_neutral_01">
        <td width="5%" >{$img.id}<a href="ord_unit_status.php?ord=id">OrderList</a>{$img.id}</td>
        <td width="5%" >{$img.id}<a href="ord_unit_status.php?ord=id">UnitList</a>{$img.id}</td>
        <td width="30%" >{$img.name}<a href="ord_unit_status.php?ord=name">Template</a>{$img.name}</td>
        <td width="30%" >{$img.value}<a href="ord_unit_status.php?ord=value">Production</a>{$img.value}</td>
        <td width="30%">{$img.value}<a href="ord_unit_status.php?ord=value">Installation</a>{$img.value}</td>
    </tr>
    {assign var="columns" value="0,1,2,3,4"}
    {section name=dat loop=$order}
        {section name=i loop=$order[dat].ord_unit}
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
            <td align="left" class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="5%" height="23">
                {if $smarty.section.i.index == 0 }{$img.id}<a href="ord_unit_status1.php?ord={$order[dat].id}">{$order[dat].id}</a>{else}{''}{/if}</td>
            <td class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="5%" height="23">
                {$order[dat].ord_unit[i].id_ord_unit}</td>
            <td class="row{$_j_}{cycle name="parity" values="1,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="30%" height="23">
                {if $order[dat].ord_unit[i].c_status == 0}{'Cutting not start yet.'}<br>
                {elseif $order[dat].ord_unit[i].c_status == 1}{'In process '}{$order[dat].ord_unit[i].c_operator}<br>{'START '}{$order[dat].ord_unit[i].c_start_time}
                {elseif $order[dat].ord_unit[i].c_status == 2}{'Finished '}{$order[dat].ord_unit[i].c_operator}
                    <br>{'START '}{$order[dat].ord_unit[i].c_start_time}
                    {'- END '}{$order[dat].ord_unit[i].c_fin_time}
                {/if}
            </td>
            <td class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="30%" height="23">
                {if $order[dat].ord_unit[i].p_status == 0}{'Production not start yet.'}<br>
                {elseif $order[dat].ord_unit[i].p_status == 1}{'In process '}{$order[dat].ord_unit[i].p_operator}<br>{' START '}{$order[dat].ord_unit[i].p_start_time}
                {elseif $order[dat].ord_unit[i].p_status == 2}{'Finished '}{$order[dat].ord_unit[i].p_operator}
                    <br>{' START '}{$order[dat].ord_unit[i].p_start_time}
                    {'- END '}{$order[dat].ord_unit[i].p_fin_time}
                {/if}
            </td>
            <td class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="30%" height="23">
                {if $order[dat].ord_unit[i].i_status == 0}{'Installation not start yet.'}<br>
                {elseif $order[dat].ord_unit[i].i_status == 1}{'In process '}{$order[dat].ord_unit[i].i_operator}<br>{' START '}{$order[dat].ord_unit[i].i_start_time}
                {elseif $order[dat].ord_unit[i].i_status == 2}{'Finished '}{$order[dat].ord_unit[i].i_operator}
                    <br>{' START '}{$order[dat].ord_unit[i].i_start_time}
                    {'- END '}{$order[dat].ord_unit[i].i_fin_time}
                {/if}
            </td>
        </tr>
        {/section}
    {/section}
    {include file="table_footer.tpl"}
    <br><center>{$paging}</center>
{else}
    <b><font color="red">No statuses defined.</font></b>
{/if}
<br>
{include file="foot.tpl"}