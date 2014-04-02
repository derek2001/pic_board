{if !$smarty.section.unt.first}<div style="page-break-before:always;font-size:1;margin:0;border:0;"><span style="visibility: hidden;">-</span></div>{/if}
<TABLE cellPadding=0 width=670 align=center bgColor=#ffffff border=0>
  <TBODY>
  <TR>
    <TD align=middle colSpan=2>
      <TABLE cellSpacing=1 cellPadding=1 width="100%" align=center 
      bgColor=#000000 border=0>
        <TBODY>
        <TR>
          <TD bgColor=#ffffff>
            <TABLE cellPadding=2 width="100%" align=center border=0>
              <TBODY>
              <TR>
                <TD align=left colSpan=2 style="FONT: 30px arial,verdana,tahoma"><B>Work Order  Id:</B>&nbsp;{$data.order.id|string_format:"%06.0f"}</TD>
                <TD align=right style="FONT: 26px arial,verdana,tahoma"><B>Unit Id:</B>&nbsp;{$data.unit[unt].id|string_format:"%06.0f"}</TD></TR>
              <TR>
                <TD align=left class="ptd13"><B>Templater Name:&nbsp;</B>{$data.unit[unt].temp[0].templater}</TD>
                <TD align="center" class="ptd13"><B>Template Date:&nbsp;</B>{$data.unit[unt].temp[0].temp_date|date_format:'%m/%d/%Y'}</TD>
                <!--<TD align=left class="ptd13"><B>Time:&nbsp;</B>{if isset($data.unit[unt].temp[0].s_interval) && $data.unit[unt].temp[0].s_interval>0}{math|date_format:'%H:%M%p' equation='x*y' x = $data.unit[unt].temp[0].s_interval y=180}{else}Any time{/if}</TD></TR>-->
              	<TD align=left class="ptd13"><B>Time:&nbsp;{$data.unit[unt].temp[0].temp_time} - {$data.unit[unt].temp[0].temp_time_to}</B><br />
												<b>STOP# {$data.unit[unt].temp[0].order+1}</b></TD></TR>
              </TBODY></TABLE></TD></TR></TBODY></TABLE><BR>

      <TABLE id=Table1 cellPadding=2 width="100%" align=center border=0>
        <TBODY>
        <TR>
          <TD align=middle width="30%">
                        {if is_array($data.unit[unt].edge) && count($data.unit[unt].edge)>0}
                        <table width="100%"><tr><td valign="top" class="ptd13" width="30">Edges:</td><td valign="top">
                        <table border="0" class="ptd11" width="100%" cellspacing="2" cellpadding="0">
                           {section name=row loop=$data.unit[unt].edge step=4}
                           <tr>
                              {section name=row2 start=$smarty.section.row.index loop=$data.unit[unt].edge max=4}
                              <td width=140 class="ptd12" style="text-align: left;" background="upload/edge/{$data.unit[unt].edge[row2].id_edge}_{$data.unit[unt].edge[row2].picture}" height=94 valign="middle" align="center" style="background-repeat: no-repeat;">
                                <b> {$data.unit[unt].edge[row2].name|escape:"htmlall"}</b>
                              </td>
                              {/section}
                           </tr>
                           {/section}
                        </table>
                        </td></tr></table>
                        {/if}
            </TD></tr>
        </TBODY></TABLE>

        </TD></TR>

  <TR><TD colSpan=2 height="1" valign="top" width="100%" bgcolor="#7f7f7f"></TD></TR>
  <TR>
    <TD vAlign=top width="47%">
      <TABLE borderColor=#ffffff cellSpacing=2 cellPadding=1 width="100%" 
      border=0>
        <TBODY>
        <TR>
          <TD align=middle colSpan=2 class="ptd13"><B><U>CLIENT INFO</U></B></SPAN> <BR></TD></TR>
        <TR>
          <TD width="35%" class="ptd12" nowrap>Client Name:&nbsp;</SPAN></TD>
          <TD width="65%" style="FONT: 18px arial,verdana,tahoma"><B>{$data.order.name}</B></TD></TR>
        <TR>
          <TD class="ptd12" nowrap>Address:&nbsp;</TD>
          <TD style="FONT: 18px arial,verdana,tahoma">{$data.order.address}</TD></TR>
        <TR>
          <TD class="ptd12">Town:&nbsp;</TD>
          <TD style="FONT: 18px arial,verdana,tahoma">{$data.order.town}</TD></TR>
        <TR>
          <TD class="ptd12">State:&nbsp;</TD>
          <TD style="FONT: 16px arial,verdana,tahoma">{$data.order.state}</TD></TR>
        <TR>
          <TD class="ptd12">Zip:&nbsp;</TD>
          <TD style="FONT: 16px arial,verdana,tahoma">{$data.order.zip}</TD></TR>
        {*<TR>
          <TD class="ptd12" nowrap>Home Phone:&nbsp;</TD>
          <TD style="FONT: 16px arial,verdana,tahoma">{$data.order.phone}</TD></TR>
		<TR>
          <TD class="ptd12" nowrap>Work Phone:&nbsp;</TD>
          <TD style="FONT: 16px arial,verdana,tahoma">{$data.order.w_phone}</TD></TR>
        <TR>
          <TD class="ptd12" nowrap>CellPhone:&nbsp;</SPAN></TD>
          <TD style="FONT: 16px arial,verdana,tahoma">{$data.order.cell}</TD></TR>
*}       {if $data.order.is_contractor}
                <TR>
                    <TD class="ptd12" nowrap><B>Company Name:</B>&nbsp;</TD>
                    <TD class="ptd12"><font color="red"><b>{$data.order.company_name}</b></font></TD>
                </TR>
                {/if}
         </TBODY></TABLE></TD>
    <TD vAlign=top width="53%">
      <TABLE borderColor=#ffffff cellSpacing=2 cellPadding=1 width="100%" 
      border=1>
        <TBODY>
        <TR>
          <TD align=center colSpan=2 class="ptd13"><B><U>UNIT INFO</U></B><BR></TD></TR>
        <TR>
          <TD width="38%" class="ptd12">Unit Name:&nbsp;</TD>
          <TD borderColor="#000000" width="62%" class="ptd12">&nbsp;{$data.unit[unt].name} (Id={$data.unit[unt].id})</TD></TR>
        <TR>
          <TD class="ptd12" nowrap>Application Name:&nbsp;</TD>
          <TD borderColor="#000000" class="ptd12">&nbsp;{$data.unit[unt].app_name}</TD></TR>
        <TR>
          <TD class="ptd12" nowrap>Stone Name:&nbsp;</TD>
          <TD borderColor="#000000" class="ptd12">&nbsp;{$data.unit[unt].slab.st_name}</TD></TR>
        <TR>
          <TD class="ptd12" nowrap>Stone Location:&nbsp;</TD>
          <TD borderColor="#000000" class="ptd12">&nbsp;{$data.unit[unt].slab.sign} ({$data.unit[unt].slab.thickness})</TD></TR>
        <TR>
          <TD class="ptd12" nowrap>Backsplash:&nbsp;</TD>
          <TD borderColor="#000000" class="ptd12">&nbsp;Standard ({$data.unit[unt].backsplash}")
          </TD></TR>
        <TR>
          <TD class="ptd12" nowrap>Estimate ID:&nbsp;</TD>
          <TD borderColor="#000000" class="ptd12">&nbsp;{if is_null($data.order.id_estimate) || $data.order.id_estimate==''}Not assigned{else}{$data.order.id_estimate}{/if}</TD></TR>
        {if $estimate.area>0}
        <TR>
          <TD class="ptd12">Estimate area:&nbsp;</TD>
          <TD borderColor=#000000 class="ptd12">{$estimate.area}sqft</TD></TR>
        {/if}
        <TR>
          <TD class="ptd12" nowrap>Payment type:&nbsp;</TD>
          <TD borderColor="#000000" class="ptd12">&nbsp;{$data.order.pay_level}</TD></TR>
        <TR>
          <TD class="ptd12" nowrap>Deposit:&nbsp;</TD>
          <TD borderColor="#000000" class="ptd12">&nbsp;</TD></TR>
        {section name=cutout loop=$data.unit[unt].cutout}
        <TR>
          <TD class="ptd12" nowrap>{$data.unit[unt].cutout[cutout].name}:&nbsp;</TD>
          <TD borderColor="#000000" class="ptd12">
             {if ($data.unit[unt].cutout[st].template)==0}&nbsp;<b><i>Object on Shop</i></b>{/if}
                {if ($data.unit[unt].cutout[st].template)==1}&nbsp;<b><i>Object not on Shop</i></b>{/if}
                {if ($data.unit[unt].cutout[st].template)==2}&nbsp;<b><i>Template on Shop</i></b>{/if}
                {if ($data.unit[unt].cutout[st].template)==3}&nbsp;<b><i>Drop-in Measurements</i></b>{/if}
                 ({$data.unit[unt].cutout[cutout].quantity})





          </TD></TR>
        {/section}
        <!--
        <TR>
          <TD class="ptd12"><B>Cutout Status:</B>&nbsp;</TD>
          <TD borderColor=#000000 class="ptd12">&nbsp;{$data.unit[unt].no_template}</TD></TR>
        <TR>
          <TD class="ptd12"><B>Client Cutout Type:</B>&nbsp;</TD>
          <TD borderColor=#000000 class="ptd13">
            {if is_array($data.unit[unt].cutout) && count($data.unit[unt].cutout)>0}
            {section name=st loop=$data.unit[unt].cutout}&nbsp;{$data.unit[unt].cutout[st].name} ({$data.unit[unt].cutout[st].quantity}){if !$smarty.section.st.last},{/if}{/section}
            {else}&nbsp;{/if}
          </TD></TR>
        <TR>
          <TD class="ptd12"><B>Sink Reveal:</B>&nbsp;</TD>
          <TD borderColor=#000000 class="ptd13">
          {if is_array($data.unit[unt].cutout) && count($data.unit[unt].cutout)>0}
          {section name=st loop=$data.unit[unt].cutout}&nbsp;{$data.unit[unt].cutout[st].r_name} ({$data.unit[unt].cutout[st].quantity}){if !$smarty.section.st.last},{/if}{/section}
          {else}&nbsp;{/if}
          </TD></TR>
        -->
        <TR>
          <TD><SPAN style="FONT: 12px arial,verdana,tahoma">Surface Finish:&nbsp;</SPAN></TD>
          <TD borderColor=#000000><SPAN 
            style="FONT: 13px arial,verdana,tahoma"><INPUT 
            id=rdoSurfaceFinishPolished type=radio CHECKED 
            value=rdoSurfaceFinishPolished name=rdoSurfaceFinish><LABEL 
            for=rdoSurfaceFinishPolished>Polished</LABEL>&nbsp;&nbsp;&nbsp; 
            &nbsp; <INPUT id=rdoSurfaceFinishHoned type=radio 
            value=rdoSurfaceFinishHoned name=rdoSurfaceFinish><LABEL 
            for=rdoSurfaceFinishHoned>Honed</LABEL></SPAN></TD></TR>
             <tr><td>{if $data.unit[unt].temp[0].message==1}{if $data.unit[unt].temp[0].m_voicemail==1}<img src="gfx/task_confirmed_vm.gif" />{else}<img src="gfx/task_confirmed.gif" />{/if}{/if}</td></tr>
        <!--
        <TR>
          <TD class="ptd12">Cut By:&nbsp;&nbsp;</TD>
          <TD borderColor=#000000 class="ptd13">&nbsp;{$data.unit[unt].cutter.c_name}</TD></TR>
        -->
        <TR align="middle" valign="middle">
            <TD><SPAN style="FONT: 12px arial,verdana,tahoma">Barcode:&nbsp;</SPAN></TD>
            <TD align="middle">{"`$data.unit[unt].id`-0"|barcode}</TD>
        </TR>
        </TBODY></TABLE></TD></TR>
  <TR><TD colSpan=2 height="1" valign="top" width="100%" bgcolor="#7f7f7f"></TD></TR>
  <TR>
    <TD colSpan=2>
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          
          <TD nowrap style="FONT: 16px arial,verdana,tahoma"><BR><B>Payment Type:&nbsp;</B>
          {$data.order.pay_level}<BR><div style="FONT: 16px arial,verdana,tahoma">
            <B>Balance:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</B>{if $data.order.dep_required}FINAL PAYMENT REQUIRED{/if}</div><BR></TD>
                <TD>
            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
              <TBODY>
              <TR>
                <TD align=right width="70%"></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE></TD></tr>
  <TR><TD colSpan=2 height="1" valign="top" width="100%" bgcolor="#7f7f7f"></TD></TR>
  <TR>
    <TD vAlign=top colSpan=2>
      {if is_array($direction) && is_array($direction.way) && count($direction.way)>0}
      <TABLE cellSpacing=1 cellPadding=1 border=0 width="100%">
        <TBODY>
        <TR>
          <TD colSpan=2><SPAN style="FONT: 13px arial,verdana,tahoma"><B>Directions:&nbsp;</B></SPAN> 
          </TD></TR>
        <TR>
          <td width="30"></td>
          <TD width="100%">
            <table width="100%" border="0">
                {section name=dir loop=$direction.way}
                <tr>
                    <td align="right" width="5%"><b>{$smarty.section.dir.index_next}. </b></td>
                    <td width="75%">{$direction.way[dir][0]}</td>
                    <td width="20%">{$direction.way[dir][1]} miles</td>
                </tr>
                {/section}
            </table>
          </TD></TR>
        </TBODY>
      </TABLE><BR>
      {/if}
      <TABLE cellSpacing=1 cellPadding=1 width="100%" border=0>
        <TBODY>
        <TR>
          <TD >
                   {*if is_array($data.log.account) && count($data.log.account)>0}
              <TABLE cellSpacing="0" cellPadding="0" width="100%" border="0"><TBODY>
              <TR><TD style="FONT: 12px arial,verdana,tahoma" align="left"><B>Account logs:</B></TD></TR>
              <TR><TD vAlign="top" align="right">
                  <TABLE style="FONT-SIZE: x-small; WIDTH: 90%; FONT-FAMILY: Arial; BORDER-COLLAPSE: collapse;align: right" cellSpacing="0" border="1">
                  <TBODY>
                      <TR>
                              <TD style="WIDTH: 100px"><b>Created By</b></TD>
							  <TD style="WIDTH: 90px"><b>Create Date</b></TD>
                              <TD><b>Note</b></TD>
                      </TR>
                                     {section name=lg loop=$data.log.account}
                      <TR>
                              <TD style="WIDTH: 100px">{$data.log.account[lg].w_name}</TD>
							  <TD style="WIDTH: 90px">{$data.log.account[lg].cr_date|date_format:"%D %H:%M"}</TD>
                              <TD>{$data.log.account[lg].description}</TD>
                      </TR>
                      {/section}
                  </TBODY></TABLE>
              </TD></TR></TBODY></TABLE>
              {/if*}
              {if is_array($data.log.wo) && count($data.log.wo)>0}
              <TABLE cellSpacing="0" cellPadding="0" width="100%" border="0"><TBODY>
              <TR><TD style="FONT: 12px arial,verdana,tahoma" align="left"><B>Work order logs:</B></TD></TR>
              <TR><TD vAlign="top" align="right">
                  <TABLE style="FONT-SIZE: x-small; WIDTH: 90%; FONT-FAMILY: Arial; BORDER-COLLAPSE: collapse;align: right" cellSpacing="0" border="1">
                  <TBODY>
                      <TR>
                              <TD style="WIDTH: 100px"><b>Created By</b></TD>
							  <TD style="WIDTH: 90px"><b>Create Date</b></TD>
                              <TD><b>Note</b></TD>
                      </TR>
                                     {section name=lg loop=$data.log.wo}
                      {if strpos($data.log.wo[lg].description,"ap...") === false}<TR>
                              <TD style="WIDTH: 100px">{$data.log.wo[lg].w_name}</TD>
							  <TD style="WIDTH: 100px">{$data.log.wo[lg].cr_date|date_format:"%D %H:%M"}</TD>
                              <TD>{$data.log.wo[lg].description}</TD>
                      </TR>{/if}
                      {/section}
                  </TBODY></TABLE>
              </TD></TR></TBODY></TABLE>
              {/if}
              
              {if is_array($data.unit[unt].log) && count($data.unit[unt].log)>0}
              <TABLE cellSpacing="0" cellPadding="0" width="100%" border="0"><TBODY>
              <TR><TD style="FONT: 12px arial,verdana,tahoma" align="left"><B>WO Unit logs:</B></TD></TR>
              <TR><TD vAlign="top" align="right">
                  <TABLE style="FONT-SIZE: x-small; WIDTH: 90%; FONT-FAMILY: Arial; BORDER-COLLAPSE: collapse;align: right" cellSpacing="0" border="1">
                  <TBODY>
                      <TR>
                              <TD style="WIDTH: 100px"><b>Created By</b></TD>
							  <TD style="WIDTH: 90px"><b>Create Date</b></TD>
                              <TD><b>Note</b></TD>
                      </TR>
                      {section name=ulgg loop=$data.unit[unt].log} 
                      <TR>
                              <TD style="WIDTH: 100px">{$data.unit[unt].log[ulgg].w_name}</TD>
							  <TD style="WIDTH: 100px">{$data.unit[unt].log[ulgg].cr_date|date_format:"%D %H:%M"}</TD>
                              <TD>{$data.unit[unt].log[ulgg].description}</TD>
                      </TR>
                      {/section}
                  </TBODY></TABLE>
              </TD></TR></TBODY></TABLE>
              {/if}
                                                                                                     
                      </TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE>
<DIV></DIV>




<!--estimate drawings-->
{assign var="page_cnt" value=$data.unit[unt].drawing_cnt}
{assign var="act_page" value=1}
{if $smarty.section.unt.first}
{capture assign="page_cnt"}{math equation="x+y+a+b" x=$page_cnt y=$data.order.drawing_cnt a=$data.estimate.drawing_cnt b=$data.estimate.unit_drawing_cnt}{/capture}
{section name=edraw loop=$data.estimate.drawing}
<div style='page-break-before:always;font-size:1;margin:0;border:0;'><span style='visibility: hidden;'>-</span></div>
<TABLE cellSpacing=1 cellPadding=1 width="670" align=center bgColor=#000000 border=0>
<TBODY>
       <TR>
              <TD bgColor=#ffffff>
                     
                     <TABLE cellSpacing=2 cellPadding=2 width="100%" align=center border=0>
            <TBODY>
                            <TR>
                       <TD align=left style="FONT: 30px arial,verdana,tahoma"><B>WO Id:&nbsp;</B>{$data.order.id|string_format:"%06.0f"}&nbsp;&nbsp;&nbsp;Est. Id:&nbsp;</B>{$data.estimate.drawing[edraw].id_estimate|string_format:"%06.0f"}&nbsp;&nbsp;&nbsp;
                       <div style="FONT: 16px arial,verdana,tahoma"></div></TD>
                       <TD align=right style="FONT: 16px arial,verdana,tahoma"><I>{$act_page} of {$page_cnt}{capture assign="act_page"}{math equation="x+1" x=$act_page}{/capture}</I></TD>
                </TR>
            </TBODY>
            </TABLE>
              
              </TD>
       </TR>
       <tr>
              <td height="750" valign="top" bgColor=#ffffff >

                     <TABLE cellSpacing=0 cellPadding=0 width=670 align=center border=0>
                        <!--<tr class="pdtf"><td width="20%" nowrap valign="top"><b>{$data.estimate.drawing[edraw].name}</b></td><td valign="top">{$data.estimate.drawing[edraw].description}</td></tr>-->
                               <tr>
                             <td colspan="2" height="4"></td>
                      </tr>
                      <TR>
                               <td colspan="2"><img src="upload/estimate/{$data.estimate.drawing[edraw].id}_{$data.estimate.drawing[edraw].picture}" width="{$data.estimate.drawing[edraw].w}" height="{$data.estimate.drawing[edraw].h}" border="0"></td>
                      </TR>
                     </TABLE>
                     
              </td>
       </tr>
</tbody>
</table>
{/section}
{section name=edraw loop=$data.estimate.unit_drawing}
<div style='page-break-before:always;font-size:1;margin:0;border:0;'><span style='visibility: hidden;'>-</span></div>
<TABLE cellSpacing=1 cellPadding=1 width="670" align=center bgColor=#000000 border=0>
<TBODY>
       <TR>
              <TD bgColor=#ffffff>
            
            <TABLE cellSpacing=2 cellPadding=2 width="100%" align=center border=0>
            <TBODY>
                            <TR>
                       <TD align=left style="FONT: 30px arial,verdana,tahoma"><B>WO Id:&nbsp;</B>{$data.order.id|string_format:"%06.0f"}&nbsp;&nbsp;&nbsp;Est. Unit Id:&nbsp;</B>{$data.estimate.unit_drawing[unt].id_est_unit|string_format:"%06.0f"}</TD>
                       <TD align=right style="FONT: 16px arial,verdana,tahoma"><I>{$act_page} of {$page_cnt}{capture assign="act_page"}{math equation="x+1" x=$act_page}{/capture}</I></TD>
                </TR>
                     </TBODY>
                     </TABLE>
                     
              </TD>
       </TR>
              <tr>
              <td height="750" valign="top" bgColor=#ffffff >

                     <TABLE cellSpacing=0 cellPadding=0 width=670 align=center border=0>
                            <!--<tr class="pdtf"><td width="20%" nowrap valign="top"><b>{$data.estimate.unit_drawing[odraw].name}</b></td><td valign="top">{$data.estimate.unit_drawing[edraw].description}</td></tr>-->
                            <tr>
                                   <td colspan="2" height="4"></td>
                            </tr>
                            <TR>
                                   <td colspan="2"><img src="upload/est_unit/{$data.estimate.unit_drawing[edraw].id}_{$data.estimate.unit_drawing[edraw].picture}" width="{$data.estimate.unit_drawing[edraw].w}" height="{$data.estimate.unit_drawing[edraw].h}" border="0"></td>
                            </TR>
                     </TABLE>
              </td>
       </tr>
</tbody>
</table>
{/section}
{/if}

{*<!--Do not print WO drawings-->
<!--order drawings-->
{if $smarty.section.unt.first}
{section name=odraw loop=$data.order.drawing}
<div style='page-break-before:always;font-size:1;margin:0;border:0;'><span style='visibility: hidden;'>-</span></div>
    <TABLE cellSpacing=1 cellPadding=1 width="670" align=center bgColor=#000000 border=0>
     <TBODY><TR>
          <TD bgColor=#ffffff>
            <TABLE cellSpacing=2 cellPadding=2 width="100%" align=center border=0>
              <TBODY>
              <TR>
                <TD align=left style="FONT: 30px arial,verdana,tahoma"><B>Work Order Id:&nbsp;</B>{$data.order.id|string_format:"%06.0f"}&nbsp;&nbsp;&nbsp;
                <div style="FONT: 16px arial,verdana,tahoma"><B>Unit Id:&nbsp;</B>{$data.unit[unt].id|string_format:"%06.0f"}</div></TD>
                <TD align=right style="FONT: 16px arial,verdana,tahoma"><I>{$act_page} of {$page_cnt}{capture assign="act_page"}{math equation="x+1" x=$act_page}{/capture}</I></TD></TR></TBODY></TABLE></TD></TR>
   </TBODY></TABLE>

   <TABLE cellSpacing=0 cellPadding=0 width=670 align=center border=0>
      <TBODY>
        <tr class="pdtf"><td width="20%" nowrap valign="top"><b>{$data.order.drawing[odraw].name}</b></td><td valign="top">{$data.order.drawing[odraw].description}</td></tr>
        <tr><td colspan="2" height="4"></td></tr>
        <TR>
          <td colspan="2"><img src="upload/order/{$data.order.drawing[odraw].id}_{$data.order.drawing[odraw].picture}" width="{$data.order.drawing[odraw].w}" height="{$data.order.drawing[odraw].h}" border="0"></td>
        </TR>
      </TBODY>
    </TABLE>
{/section}
{/if}

<!--unit drawings-->
{section name=udraw loop=$data.unit[unt].drawing}
<div style='page-break-before:always;font-size:1;margin:0;border:0;'><span style='visibility: hidden;'>-</span></div>
    <TABLE cellSpacing=1 cellPadding=1 width="670" align=center bgColor=#000000 border=0>
     <TBODY><TR>
          <TD bgColor=#ffffff>
            <TABLE cellSpacing=2 cellPadding=2 width="100%" align=center border=0>
              <TBODY>
              <TR>
                <TD align=left style="FONT: 30px arial,verdana,tahoma"><B>Work Order Id:&nbsp;</B>{$data.order.id|string_format:"%06.0f"}&nbsp;&nbsp;&nbsp;
                <div style="FONT: 16px arial,verdana,tahoma"><B>Unit Id:&nbsp;</B>{$data.unit[unt].id|string_format:"%06.0f"}</div>&nbsp;&nbsp;&nbsp;&nbsp;
                <div class="ptd12"><B>Name:&nbsp;</B>{$data.unit[unt].name}</div></TD>
                <TD align=right style="FONT: 16px arial,verdana,tahoma"><I>{$act_page} of {$page_cnt}{capture assign="act_page"}{math equation="x+1" x=$act_page}{/capture}</I></TD></TR></TBODY></TABLE></TD></TR>
   </TBODY></TABLE>

   <TABLE cellSpacing=0 cellPadding=0 width=670 align=center border=0>
      <TBODY>
        <tr class="pdtf"><td width="20%" nowrap valign="top"><b>{$data.unit[unt].drawing[udraw].name}</b></td><td valign="top">{$data.unit[unt].drawing[udraw].description}</td></tr>
        <tr><td colspan="2" height="4"></td></tr>
        <TR>
          <td colspan="2"><img src="upload/ord_unit/{$data.unit[unt].drawing[udraw].id}_{$data.unit[unt].drawing[udraw].picture}" width="{$data.unit[unt].drawing[udraw].w}" height="{$data.unit[unt].drawing[udraw].h}" border="0"></td>
        </TR>
      </TBODY>
    </TABLE>
{/section}

<div style='page-break-before:always;font-size:1;margin:0;border:0;'><span style='visibility: hidden;'>-</span></div>    *}
<img src="gfx/template_print.png" border="0" width="810" />
{if $data.order.state == 'NY'}
<div style='page-break-before:always;font-size:1;margin:0;border:0;'><span style='visibility: hidden;'>-</span></div>
{include file="print/nycapitalimprov.tpl"}
{/if}