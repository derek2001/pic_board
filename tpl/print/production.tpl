{if !$smarty.section.unt.first}<div style="page-break-before:always;font-size:1;margin:0;border:0;"><span style="visibility: hidden;">-</span></div>
{/if}
<TABLE cellPadding=0 width=670 align=center bgColor=#ffffff border=0>
  <TBODY>
  <TR>
    <TD align=middle colSpan=2>
      <TABLE cellSpacing=1 cellPadding=1 width="100%" align=center bgColor=#000000 border=0>
        <TBODY>
        <TR>
          <TD bgColor=#ffffff>
            <TABLE cellPadding=2 width="100%" align=center border=0>
              <TBODY>
              <TR>
                <TD align=left colSpan=2 style="FONT: 30px arial,verdana,tahoma"><B>Work Order  Id:</B>&nbsp;{$data.order.id|string_format:"%06.0f"}</TD>
                <TD align=right style="FONT: 26px arial,verdana,tahoma"><B>Unit Id:</B>&nbsp;{$data.unit[unt].id|string_format:"%06.0f"}</TD></TR>
              <TR>
                <TD align=left class="ptd13"><B>Templater Name:&nbsp;</B>{section name=tmp loop=$data.unit[unt].temp}
                    {if !$smarty.section.tmp.first},{/if}{$data.unit[unt].temp[tmp].templater}{/section}</TD>
                <TD align="center" class="ptd13"><B>Template Date:&nbsp;</B>{section name=tmp loop=$data.unit[unt].temp}
                    {if !$smarty.section.tmp.first},{/if}{$data.unit[unt].temp[tmp].temp_date|date_format:'%m/%d/%Y'}{/section}</TD>
                <!--<TD align=left class="ptd13"><B>Time:&nbsp;</B>{if isset($data.unit[unt].temp[0].s_interval) && $data.unit[unt].temp[0].s_interval>0}{math|date_format:'%H:%M%p' equation='x*y' x = $data.unit[unt].temp[0].s_interval y=180}{else}Any time{/if}</TD></TR>-->
              	<TD align=left class="ptd13"><B>Time:&nbsp;</B>{$data.unit[unt].temp[0].temp_time} - {$data.unit[unt].temp[0].temp_time_to}</TD></TR>
              </TBODY></TABLE></TD></TR></TBODY></TABLE>

      <TABLE id=Table1 cellPadding=1 width="100%" align=center border=0>
        <TBODY>
          <tr><TD width="70%" align=left style="FONT: 20px arial,verdana,tahoma" nowrap><B>Stone Name:&nbsp;</B>{$data.unit[unt].slab.st_name}&nbsp;&nbsp;{$data.unit[unt].slab.sign} - {$data.unit[unt].slab.thickness}
                  {if strripos($data.unit[unt].slab.st_name, 'Blue Pearl') !== false}<br><span style="background-color: black; color:white; font-weight: bold">PAY ATTENTION TO DIRECTION OF THE FLAKES</span>{/if}</td>
                  {if $data.unit[unt].slab.sign != '' && $data.unit[unt].status.status < 5}
                      {if $data.unit[unt].cnt_pic.cnt_raw<1 && $data.unit[unt].cnt_pic.cnt_erp<1 && $data.unit[unt].cnt_pic.cnt_www<3
                        && $data.unit[unt].cnt_pic.cnt_jpg<1 && $data.unit[unt].cnt_pic.www_pc1==''
                        && $data.unit[unt].cnt_pic.www_pc2=='' && $data.unit[unt].cnt_pic.www_pc3==''}
                          <td align=right valign="top" style="FONT: 35px arial,verdana,tahoma"  nowrap>
                                  <TABLE bgColor=#000000 width="30%">
                                      <TBODY><tr><td style="FONT: 35px arial,verdana,tahoma"><span style=" color: white; font-weight: bold">Picture</span></tr></td></TBODY>
                                  </TABLE></td>
                      {/if}
                  {/if}</tr>
          <tr><td align=left style="FONT: 14px arial,verdana,tahoma" nowrap><B>Installation Date:&nbsp;</B>{section name=ist loop=$data.unit[unt].inst}
                  {if !$smarty.section.ist.first},{/if}<b><u>{$data.unit[unt].inst[ist].inst_date|date_format:'%m/%d/%Y'}</b></u> (<b><u>{$data.unit[unt].inst[ist].installer}</b></u>){/section}</td>
          </tr>
          <tr><td align="left" nowrap width="100%">
            <table cellspacing="0" cellpadding="0" width="100%"><tr>
                <td align="left" width="50%" style="FONT: 14px arial,verdana,tahoma" nowrap><B>Yard Salesman:&nbsp;</B>{$data.order.ysalesman}</td>
                <td align="left" width="50%" style="FONT: 14px arial,verdana,tahoma" nowrap><B>Cutter:&nbsp;</B>{section name=cut loop=$data.unit[unt].cutter}{if !$smarty.section.cut.first},{/if}{$data.unit[unt].cutter[cut].c_name}{/section}</td>
            </tr></table>
          </TD></TR></tbody></table>
        </td></tr>
  
        {if is_array($data.unit[unt].edge) && count($data.unit[unt].edgec)>0} 
        <TR><TD height="1" valign="top" width="100%" align="left" bgcolor="#7f7f7f" colSpan=2></TD></TR>
        <TR>
          <TD align=middle width="100%" colspan="2">
                                          
                                                                  
                                   <!-- Displaying edges -->
                                 <table border="0" class="ptd11" width="100%" cellspacing="2" cellpadding="0">
                                 <tr><td width="50%" valign="top" nowrap>
                                  <!-- Application edges -->
                                  <span style="FONT: 13px arial,verdana,tahoma">{$data.unit[unt].app_name} edges:</span>
                                  <table border="0" class="ptd11" width="100%" cellspacing="2" cellpadding="0">
                                  <tr>
								{assign var="ledge" value=$data.unit[unt].edge.co_count} 
								 {if $ledge!=1}
								   { if $ledge} {assign var="ledge" value=4} {/if}
								    {assign var="a" value=0} 
								     {section name=row2 start=$smarty.section.row.index loop=$data.unit[unt].edgec max=$ledge}
                                       {assign var="a" value=$a+1} 
									   { if $a==3} </tr> <tr>{/if}
									   {*if $data.unit[unt].edgec[row2].id_edge!=69*}
									   <td width=140 class="ptd12" style="text-align: left;" background="upload/edge/{$data.unit[unt].edgec[row2].id_edge}_{$data.unit[unt].edgec[row2].picture}" height=94 valign="middle" align="left" style="background-repeat: no-repeat;">
                                <b> {$data.unit[unt].edgec[row2].name|escape:"htmlall"}<br />Length: {$data.unit[unt].edgec[row2].length|escape:"htmlall"} ft.</b>
                                        </td> {*/if*}
									{/section}
								   
								 {else}
								     <td width=140 class="ptd12" style="text-align: left;" background="upload/edge/{$data.unit[unt].edgec[0].id_edge}_{$data.unit[unt].edgec[0].picture}" height=94 valign="middle" align="left" style="background-repeat: no-repeat;">
                                <b> {$data.unit[unt].edgec[0].name|escape:"htmlall"}</b>
                                        </td>
								 {/if}	
								   </tr>
								   </table>
                                    
                                  
								  
								  
								  <!-- Backsplash edges -->                                             
                                  </td><td width="50%" valign="top" nowrap>
                                  <span style="FONT: 13px arial,verdana,tahoma">Backsplash edges:</span>
								  <table border="0" class="ptd11" width="100%" cellspacing="2" cellpadding="0">
                                  <tr>
									 {assign var="a" value=0} 
								  {section name=row2 start=$smarty.section.row.index loop=$data.unit[unt].edgeb max=4}
                                       {assign var="a" value=$a+1} 
									     { if $a==3} </tr> <tr>{/if}
									   <td width=140 class="ptd12" style="text-align: left;" background="upload/edge/{$data.unit[unt].edgeb[row2].id_edge}_{$data.unit[unt].edgeb[row2].picture}" height=94 valign="middle" align="left" style="background-repeat: no-repeat;">
                                <b> {$data.unit[unt].edgeb[row2].name|escape:"htmlall"}</b>
                                         </td>
									 {/section}
								  </tr>
								  </table>
                                         <hr>
                                                 
                        
        </TD></TR></table>
        {/if} 
            
  <TR><TD colSpan=2 height="1" valign="top" width="100%" bgcolor="#7f7f7f"></TD></TR>
  <TR>
    <TD vAlign=middle align="center" width="47%">
	  {if $data.unit[unt].slab.slab_sample == 1}
	  <div style="border:10px dashed black;padding:2px; margin-top: 5px; font-size:26px; font-weight:bold; font-family:Verdana, Arial, Helvetica, sans-serif;">CUT SAMPLES</div><hr>
	  {/if}
      <div vAlign="middle" align="center" width="47">
          {"`$data.unit[unt].id`-1"|barcode}
      </div>

      <!--<TABLE borderColor=#ffffff cellSpacing=2 cellPadding=1 width="100%" 
      border=0>
        <TBODY>
        <TR>
          <TD align=middle colSpan=2 class="ptd13"><B><U>CLIENT INFO</U></B></SPAN> <BR></TD></TR>
        <TR>
          <TD width="35%" class="ptd12" nowrap><B>Client Name:</B>&nbsp;</SPAN></TD>
          <TD width="65%" style="FONT: 18px arial,verdana,tahoma"><B>{$data.order.name}</B></TD></TR>
        <TR>
          <TD class="ptd12" nowrap><B>Address:</B>&nbsp;</TD>
          <TD style="FONT: 18px arial,verdana,tahoma">{$data.order.address}</TD></TR>
        <TR>
          <TD class="ptd12"><B>Town:</B>&nbsp;</TD>
          <TD style="FONT: 18px arial,verdana,tahoma">{$data.order.town}</TD></TR>
        <TR>
          <TD class="ptd12"><B>State:</B>&nbsp;</TD>
          <TD style="FONT: 16px arial,verdana,tahoma">{$data.order.state}</TD></TR>
        <TR>
          <TD class="ptd12"><B>Zip:</B>&nbsp;</TD>
          <TD style="FONT: 16px arial,verdana,tahoma">{$data.order.zip}</TD></TR>
        {if $data.order.is_contractor}
        <TR>
          <TD class="ptd12" nowrap><B>Company Name:</B>&nbsp;</TD>
          <TD class="ptd12">{$data.order.company_name}</TD>
		</TR>
        {/if}
        </TBODY></TABLE>--></TD>
    <TD vAlign=top width="53%">
      <TABLE borderColor=#ffffff cellSpacing=2 cellPadding=1 width="100%" 
      border=1>
        <TBODY>
        <TR>
          <TD align=center colSpan=2 class="ptd13"><B><U>UNIT INFO</U></B><BR></TD></TR>
        <TR>
          <TD width="38%" class="ptd12"><B>Unit Name:&nbsp;</B></TD>
          <TD borderColor=#000000 width="62%" class="ptd13">&nbsp;{$data.unit[unt].name} (Id={$data.unit[unt].id})</TD></TR>
        <TR>
          <TD class="ptd12"><B>Application Name:</B>&nbsp;</TD>
          <TD borderColor=#000000 class="ptd13">&nbsp;{$data.unit[unt].app_name}</TD></TR>
          <!--
        <TR>
          <TD class="ptd12"><B>Cutout Status:</B>&nbsp;</TD>
          <TD borderColor=#000000 class="ptd12">&nbsp;{$data.unit[unt].no_template}</TD></TR>
          -->
         {if is_array($data.unit[unt].cutout) && count($data.unit[unt].cutout)>0}
            {section name=st loop=$data.unit[unt].cutout}
             <TR>
           
              <TD class="ptd12"><B>Client Cutout Type:</B>&nbsp;</TD>

          <TD borderColor=#000000 class="ptd13">&nbsp;
         
            {$data.unit[unt].cutout[st].name}
            ({$data.unit[unt].cutout[st].quantity})<br>
                {if ($data.unit[unt].cutout[st].template)==0}&nbsp;&nbsp;<b><i>Object on Shop</i></b>{/if}
                {if ($data.unit[unt].cutout[st].template)==1}&nbsp;&nbsp;<b><i>Object not on Shop</i></b>{/if}
                {if ($data.unit[unt].cutout[st].template)==2}&nbsp;&nbsp;<b><i>Template on Shop</i></b>{/if}
                {if ($data.unit[unt].cutout[st].template)==3}&nbsp;&nbsp;<b><i>Drop-in Measurements</i></b>{/if}

            
             </TD></TR>
           {/section}
          
        {/if}
        <!--
        <TR>
          <TD class="ptd12"><B>Sink Reveal:</B>&nbsp;</TD>
          <TD borderColor=#000000 class="ptd13">
          {if is_array($data.unit[unt].cutout) && count($data.unit[unt].cutout)>0}
          {section name=st loop=$data.unit[unt].cutout}&nbsp;{$data.unit[unt].cutout[st].r_name} ({$data.unit[unt].cutout[st].quantity}){if !$smarty.section.st.last},{/if}{/section}
          {else}&nbsp;{/if}
          </TD></TR>
          -->
		  {if is_array($data.product) && count($data.product)>0 }
		 <tr>
		 <td class="ptd12"><b>Products:</b></td>
		 <td borderColor="#000000" class="ptd13">
			
			{section name=prod loop=$data.product}
				{$data.product[prod].p_name} - Qty: {$data.product[prod].quantity}<br />
				{$data.product[prod].description}<br />
				{if !$smarty.section.prod.last}<hr size="1" color="#000000" />{/if}
			{/section}
			
			
		 	
		 </td>
		 </tr>
		 {/if}
        <TR>
          <TD class="ptd12"><B>Surface 
            Finish:</B></TD>
          <TD borderColor=#000000 class="ptd13"><SPAN 
            style="FONT: 13px arial,verdana,tahoma">{if $data.unit[unt].honing !=1}<INPUT 
            id=rdoSurfaceFinishPolished type="checkbox" {if $data.unit[unt].honing !=1} checked="checked" {/if} 
            value=rdoSurfaceFinishPolished name=rdoSurfaceFinish><LABEL 
            for=rdoSurfaceFinishPolished>Polished</LABEL>&nbsp;&nbsp;&nbsp;{else} 
            &nbsp; <INPUT id=rdoSurfaceFinishHoned type="checkbox" {if $data.unit[unt].honing ==1} checked="checked" {/if}
            value=rdoSurfaceFinishHoned name=rdoSurfaceFinish><LABEL 
            for=rdoSurfaceFinishHoned><b>HONED</b></LABEL>{/if}</SPAN></TD></TR>
        <TR>
          <TD class="ptd12"><B>Created By:</B>&nbsp;&nbsp;</TD>
          <TD borderColor=#000000 class="ptd13">&nbsp;{$data.unit[unt].cr_name}</TD></TR></TBODY></TABLE></TD></TR>
  <TR><TD colSpan=2 height="1" valign="top" width="100%" bgcolor="#7f7f7f"></TD></TR>
  <TR>
    <TD colSpan=2>
    
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          <TD nowrap style="FONT: 16px arial,verdana,tahoma"><BR><B>Payment Type:&nbsp;</B>
          {$data.order.pay_level}
          {if $data.order.is_contractor}
          <BR><div style="FONT: 16px arial,verdana,tahoma">
            <B>Balance:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</B>{if $data.order.dep_required}FINAL PAYMENT REQUIRED{/if}</div><BR>
          {/if}
          </TD>
          <TD>
            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
              <TBODY>
              <TR>
                <TD align=right width="70%"></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE></TD></tr>
  <TR><TD colSpan=2 height="1" valign="top" width="100%" bgcolor="#7f7f7f"></TD></TR>
  <TR>
    <TD vAlign=top colSpan=2>
      <TABLE cellSpacing=1 cellPadding=1 width="100%" border=0>
        <TBODY>
        <TR>
          <TD>
              
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
                  <TABLE style="FONT-SIZE: medium; WIDTH: 100%; FONT-FAMILY: Arial; BORDER-COLLAPSE: collapse;align: right" cellSpacing="0" border="1">
                  <TBODY>
                      <TR>
                              <TD style="WIDTH: 100px"><b>Created By</b></TD>
							  <TD style="WIDTH: 90px"><b>Create Date</b></TD>
                              <TD><b>Note</b></TD>
                      </TR>
                      {section name=lg loop=$data.log.wo}
                      {if strpos($data.log.wo[lg].description,"ap...") === false and strpos($data.log.wo[lg].description,"($") === false and strpos($data.log.wo[lg].description,"Transfer Notes") === false}<TR>
                              <TD style="WIDTH: 100px">{$data.log.wo[lg].w_name}</TD>
							  <TD style="WIDTH: 90px">{$data.log.wo[lg].cr_date|date_format:"%D %H:%M"}</TD>
                              <TD>{$data.log.wo[lg].description|upper}</TD>
                      </TR>{/if}
                      {/section}
                  </TBODY></TABLE>
              </TD></TR></TBODY></TABLE>
              {/if}
              
              {if is_array($data.unit[unt].log) && count($data.unit[unt].log)>0}
              <TABLE cellSpacing="0" cellPadding="0" width="100%" border="0"><TBODY>
              <TR><TD style="FONT: 12px arial,verdana,tahoma" align="left"><B>WO Unit logs:</B></TD></TR>
              <TR><TD vAlign="top" align="right">
                  <TABLE style="FONT-SIZE: medium; WIDTH: 100%; FONT-FAMILY: Arial; BORDER-COLLAPSE: collapse;align: right" cellSpacing="0" border="1">
                  <TBODY>
                      <TR>
                              <TD style="WIDTH: 100px"><b>Created By</b></TD>
							  <TD style="WIDTH: 90px"><b>Create Date</b></TD>
                              <TD><b>Note</b></TD>
                      </TR>
                      {section name=ulgg loop=$data.unit[unt].log}
                      <TR>
                              <TD style="WIDTH: 100px">{$data.unit[unt].log[ulgg].w_name}</TD>
							  <TD style="WIDTH: 90px">{$data.unit[unt].log[ulgg].cr_date|date_format:"%D %H:%M"}</TD>
                              <TD>
							  {if strpos(strtolower($data.unit[unt].log[ulgg].description),"sink") !== false or strpos(strtolower($data.unit[unt].log[ulgg].description),"*") !== false}
							  <font size="+2"><b>{$data.unit[unt].log[ulgg].description|upper}</b></font>
							  {else}
							  {$data.unit[unt].log[ulgg].description|upper}
							  {/if}
							  </TD>
                      </TR>
	                  {/section}
                  </TBODY></TABLE>
              </TD></TR></TBODY></TABLE>
              {/if}
                                                                                                     
                      </TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE>
              


<!--order drawings-->
{assign var="page_cnt" value=$data.unit[unt].drawing_cnt}
{assign var="act_page" value=1}
{if $smarty.section.unt.first}
{capture assign="page_cnt"}{math equation="x+y" x=$page_cnt y=$data.order.drawing_cnt}{/capture}
{section name=odraw loop=$data.order.drawing}
<div style="page-break-before:always;font-size:1;margin:0;border:0;"><span style="visibility: hidden;">-</span></div>
    <TABLE cellSpacing=1 cellPadding=1 width="670" align=center bgColor=#000000 border=0>
     <TBODY><TR>
          <TD bgColor=#ffffff>
            <TABLE cellSpacing=2 cellPadding=2 width="100%" align=center border=0>
              <TBODY>
              <TR>
                <TD align=left style="FONT: 30px arial,verdana,tahoma"><B>Work Order Id:&nbsp;</B>{$data.order.id|string_format:"%06.0f"}&nbsp;&nbsp;&nbsp;
                <div style="FONT: 16px arial,verdana,tahoma"><B>Unit Id:&nbsp;</B>{$data.unit[unt].id|string_format:"%06.0f"}</div></TD>
                <TD align=right style="FONT: 16px arial,verdana,tahoma"><I>{$act_page} of {$page_cnt}{capture assign="act_page"}{math equation="x+1" x=$act_page}{/capture}</I></TD></TR></TBODY></TABLE></TD></TR>
      <tr><td height="750" valign="top" bgColor=#ffffff >
        <table width="100%" valign="top" align="center">
        <!--<tr class="pdtf"><td width="20%" nowrap valign="top"><b>{$data.order.drawing[odraw].name}</b></td><td valign="top">{$data.order.drawing[odraw].description}</td></tr>-->
        <tr><td colspan="2" height="4"></td></tr>
        <TR>
          <td colspan="2"><img src="upload/order/{$data.order.drawing[odraw].id}_{$data.order.drawing[odraw].picture}" width="{$data.order.drawing[odraw].w}" height="{$data.order.drawing[odraw].h}" border="0"></td>
        </TR>
        </table>
        </td></tr>
      </TBODY>
    </TABLE>
{/section}
{/if}

<!--unit drawings-->
{section name=udraw loop=$data.unit[unt].drawing}
<div style="page-break-before:always;font-size:1;margin:0;border:0;"><span style="visibility: hidden;">-</span></div>
    <TABLE cellSpacing=1 cellPadding=1 width="670" align=center bgColor=#000000 border=0>
     <TBODY><TR>
          <TD bgColor=#ffffff>
            <TABLE cellSpacing=2 cellPadding=2 width="100%" align=center border=0>
              <TBODY>
              <TR>
                <TD align=left style="FONT: 30px arial,verdana,tahoma"><B>Work Order Id:&nbsp;</B>{$data.order.id|string_format:"%06.0f"}&nbsp;&nbsp;&nbsp;
                       <div style="FONT: 16px arial,verdana,tahoma"><B>Unit Id:&nbsp;</B>{$data.unit[unt].id|string_format:"%06.0f"}&nbsp;&nbsp;&nbsp;<B>Name:&nbsp;</B>{$data.unit[unt].name}</div>
                      </TD>
                <TD align=right style="FONT: 16px arial,verdana,tahoma"><I>{$act_page} of {$page_cnt}{capture assign="act_page"}{math equation="x+1" x=$act_page}{/capture}</I></TD></TR></TBODY></TABLE></TD></TR>
      <tr><td height="750" valign="top" bgColor=#ffffff >
        <table width="100%" valign="top" align="center">
        <!--<tr class="pdtf"><td width="20%" nowrap valign="top"><b>{$data.unit[unt].drawing[udraw].name}</b></td><td valign="top">{$data.unit[unt].drawing[udraw].description}</td></tr>-->
        <tr><td colspan="2" height="4"></td></tr>
        <TR>
          <td colspan="2" align="center"><img src="upload/ord_unit/{$data.unit[unt].drawing[udraw].id}_{$data.unit[unt].drawing[udraw].picture}" width="{$data.unit[unt].drawing[udraw].w}" height="{$data.unit[unt].drawing[udraw].h}" border="0"></td>
        </TR>
        </table>
        
        </td></tr>
      </TBODY>
    </TABLE>
{/section}
