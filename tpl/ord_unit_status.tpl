{include file="header.tpl"}
<script type="text/javascript" src="js/design.js"></script>
{if $error<>''}<br><b><font color="red">{$error}</font></b>{/if}

{literal}
    <script language="javascript">
        function askme(what)
        {
            return window.confirm("Are you sure you want to remove status '"+what+"'");
        }
    </script>
{/literal}

{assign var="module_name" value="Unit statuses"}
{include file="module_header.tpl"}
<br>
{assign var="table_width" value="800"}
{assign var="table_headertitle" value="Status"}
{include file="table_header.tpl"}
{if count($order)>0}
    <tr class="cell_reccolor_neutral_01">
        <td width="20%" >{$img.id}<a href="ord_unit_status.php?ord=id">OrderList</a>{$img.id}</td>
        <td width="20%" >{$img.id}<a href="ord_unit_status.php?ord=id">UnitList</a>{$img.id}</td>
        <td width="20%" >{$img.name}<a href="ord_unit_status.php?ord=name">Stage 1 Cutting</a>{$img.name}</td>
        <td width="20%" >{$img.value}<a href="ord_unit_status.php?ord=value">Stage 2 Production</a>{$img.value}</td>
        <td width="20%">{$img.value}<a href="ord_unit_status.php?ord=value">Stage 3 Installation</a>{$img.value}</td>
    </tr>
    {assign var="columns" value="0,1,2,3,4"}
    {section name=dat loop=$order}
        {section name=i loop=$order[dat].ord_unit}
        {assign var="_i_" value=$smarty.section.dat.index}
        {if $_i_ is even}{assign var="_j_" value="1"}{else}{assign var="_j_" value="0"}{/if}
        <tr align="center" valign="top" onMouseOver="RowOver({$_i_})" onMouseOut="RowOut({$_i_})">
            <td class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="20%">{if $smarty.section.i.index == 0 }{$order[dat].id}{else}{''}{/if}</td>
            <td class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="20%">{$order[dat].ord_unit[i].id}</td>
            <td class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="20%">{''}</td>
            <td class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="20%">{''}</td>
            <td class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}" width="20%">{''}</td>
        </tr>
        {/section}
    {/section}
    {include file="table_footer.tpl"}
{else}
    <b><font color="red">No statuses defined.</font></b>
{/if}
{include file="foot.tpl"}