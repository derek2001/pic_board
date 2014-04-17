{include file="header_no_user.tpl"}
<script type="text/javascript" src="js/design.js"></script>
<script type="text/javascript" src="js/ajax/jquery-1.6.2.min.js"></script>
<meta http-equiv="refresh" content="5">
{dhtml_calendar_init src='js/jscalendar/calendar.js' setup_src='js/jscalendar/calendar-setup.js' lang='js/jscalendar/lang/calendar-en.js' css='js/jscalendar/calendar-system.css'}
{if $error<>''}<br><b><font color="red">{$error}</font></b>{/if}
{literal}
<style>
.cell_td {
    background-color: #9ffaaf;
    font-family: Tahoma, Arial, Verdana;
    font-weight: bold;
    font-size: 20px;
    color: darkblue;
    text-align: center;
}

.cell_header {
    background-color: #B6BAF7;
    font-family: Tahoma, Arial, Verdana;
    font-size: 20px;
    font-weight:bold;
    color: #ffffff;
    text-align: center;
    border-spacing: 5px;
    border-right-style: solid;
    padding: 5px 5px 5px 5px;
}
.cell_table {
    background-color: #ededed;
    font-family: Tahoma, Arial, Verdana;
    font-size: 12px;
    font-weight:bold;
    color: #000066;
    text-align: center;
    border-spacing: 5px;
    border-right-style: solid;
    border: 1px;
    rules: rows;
}
</style>
<script type="text/javascript">
    function echoTime(min, id){
        var hr = Math.floor(min/60);
        var day = Math.floor(hr/24);
        var minute = min%60;
        if(day>0 || hr>=3)
            document.getElementById(id).style.backgroundColor = '#FFAEAE';

        if(day>0)
            day=day + ':';
        else
            day = '';

        hr = hr%24;

        if(hr<10)
            hr = '0'+hr;
        if(minute<10)
            minute = '0'+minute;

        var time = day + hr + ":" + minute;
        document.write(time+"");
    }
</script>
{/literal}
{assign var="module_name" value="Work Order Board"}
{include file="module_header.tpl"}
{assign var="table_width" value="1695"}

{if count($order)>0}
<table>
    <tr style="border: 1px">
        {include file="table_no_header.tpl"}
        <table width="100%" style=" border: 1px; " rules="none" >
          <tr>
            <td width="10%" class="cell_header">WORK ORDER</a></td>
            <td width="10%" class="cell_header">ORDER UNIT</a></td>
            <td width="8%" class="cell_header">LOCATION</a></td>
            <td width="7%" class="cell_header">CAMERA</a></td>
            <td width="10%" class="cell_header">A FRAME</a></td>
            <td width="20%" class="cell_header">STONE NAME</a></td>
            <td width="15%" class="cell_header">JOB TIME ELAPSED</a></td>
            <td width="20%" class="cell_header">CUTTER</a></td>
          </tr>
        </table>
        {include file="table_bottom.tpl"}
    </tr>

    {assign var="columns" value="0,1,2,3,4,5"}
    {section name=dat loop=$order}
        {assign var="_i_" value=$smarty.section.dat.index}
        <tr align="right" valign="top">
            {include file="table_no_header.tpl"}
            <table width="100%" style="border: 1px; " rules="none">
            <tr>
            <td class="cell_td" width="10%" height="28" >
                <b>{$order[dat].id_order}</b>
            </td>
            <td class="cell_td" width="10%" height="28">
                <b>{$order[dat].id_ord_unit}</b></td>
            <td class="cell_td" width="8%" height="28">
                <b>{$order[dat].id_location|location}</b>
            </td>
            <td class="cell_td" width="7%" height="28">
                <b>{$order[dat].id_location|location}</b>
            </td>
            <td class="cell_td" width="10%" height="28">
                <b>{$order[dat].sign}</b>
            </td>
            <td class="cell_td" width="20%" height="28">
                <b>{$order[dat].sname}</b>

            </td>
            <td class="cell_td" width="15%" height="28" id="{$smarty.section.dat.index}">
                <b><script>echoTime({$order[dat].time}, {$smarty.section.dat.index});</script></b>
            </td>
            <td class="cell_td" width="20%" height="28">
                <b>{$order[dat].name}</b>
            </td>
            </tr>
            </table>
            {include file="table_bottom.tpl"}
        </tr>
        {$id = $order[dat].id_order}
    {/section}

</table>
{else}
    <b><font color="red">No statuses defined.</font></b>
{/if}
<br>
<br>