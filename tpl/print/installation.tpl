{if !$smarty.section.unt.first}<div style="page-break-before:always;font-size:1;margin:0;border:0;"><span style="visibility: hidden;">-</span></div>{/if}
<TABLE cellPadding="0" width="670" align="center" bgColor="#ffffff" border="0">
	<TR>
		<TD align="middle" colSpan="2">
			<TABLE cellSpacing="1" cellPadding="1" width="100%" align="center" bgColor="#000000" border="0">
				<TR>
					<TD bgColor="#ffffff">
						<TABLE cellPadding="2" width="100%" align="center" border="0">
							<TR>
								<TD align=left colSpan=2 style="FONT: 30px arial,verdana,tahoma"><B>Work Order  Id:</B>&nbsp;{$data.order.id|string_format:"%06.0f"}</TD>
								<TD align=right style="FONT: 26px arial,verdana,tahoma"><B>Unit Id:</B>&nbsp;{$data.unit[unt].id|string_format:"%06.0f"}</TD>
							</TR>
							<TR>
								<TD align=left class="ptd13"><B>Templater Name:&nbsp;</B>{$data.unit[unt].temp[0].templater}</TD>
								<TD align="center" class="ptd13"><B>Template Date:&nbsp;</B>{$data.unit[unt].temp[0].temp_date|date_format:'%m/%d/%Y'}</TD>
								{if is_array($data.unit[unt].inst) && count($data.unit[unt].inst)>0}
								{section name=ist loop=$data.unit[unt].inst}
								<TD align=left class="ptd13"><B>Time:&nbsp;</B><b>{$data.unit[unt].inst[ist].inst_time} - {$data.unit[unt].inst[ist].inst_time_to}</b></TD>
								{/section}
								{/if}
							</TR>
							{if is_array($data.unit[unt].inst) && count($data.unit[unt].inst)>0}
							<tr>
								<td colspan="3" align=left style="FONT: 14px arial,verdana,tahoma" nowrap><B>Installation:&nbsp;</B>{section name=ist loop=$data.unit[unt].inst}{if trim($data.unit[unt].inst[ist].inst_date)!=''}{if !$smarty.section.ist.first},{/if}<b><u>{$data.unit[unt].inst[ist].inst_date|date_format:'%m/%d/%Y'}</b></u> (<b><u>{$data.unit[unt].inst[ist].installer}</b></u>)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>STOP# {$data.unit[unt].inst[ist].order+1}</strong>{/if}{/section}</td>
							</tr>
							{/if}
						</TABLE>
					</TD>
				</TR>
			</TABLE>

			<TABLE id=Table1 cellPadding=2 width="100%" align=center border=0>
				<tr>
					<TD align=left style="FONT: 20px arial,verdana,tahoma" nowrap><BR><B>Stone Name:&nbsp;</B>{$data.unit[unt].slab.st_name}&nbsp;&nbsp;{$data.unit[unt].slab.sign} - {$data.unit[unt].slab.thickness}<BR><BR></TD>
				    <TD align="right">{"`$data.unit[unt].id`-2"|barcode}</TD>
                </TR>

				{if is_array($data.unit[unt].edge) && count($data.unit[unt].edge)>0} 
				<TR>
					<TD height="1" valign="top" width="100%" bgcolor="#7f7f7f" colspan="2"></TD>
				</TR>
				<TR>
					<TD align=middle width="30%" colspan="2">
						<table width="100%">
							<tr>
								<td valign="top" class="ptd13" width="30">Edges:</td>
								<td valign="top">
									<table border="0" class="ptd11" width="100%" cellspacing="2" cellpadding="0">
										{section name=row loop=$data.unit[unt].edge step=4}
										<tr>
											{section name=row2 start=$smarty.section.row.index loop=$data.unit[unt].edge max=4}
											<td width=140 class="ptd12" style="text-align: left;" background="upload/edge/{$data.unit[unt].edge[row2].id_edge}_{$data.unit[unt].edge[row2].picture}" height=94 valign="middle" align="center" style="background-repeat: no-repeat;"><b> {$data.unit[unt].edge[row2].name|escape:"htmlall"}</b></td>
											{/section}
										</tr>
										{/section}
									</table>
								</td>
							</tr>
						</table>
					</TD>
				</tr>
				{/if}
			</TABLE>
		</TD>
	</TR>             
	<TR>
		<TD colSpan=2 height="1" valign="top" width="100%" bgcolor="#7f7f7f"></TD>
	</TR>
	<TR>
		<TD vAlign=top width="47%">
            {if $no_address == 'false'}
			<TABLE borderColor="#ffffff" cellSpacing=2 cellPadding=1 width="100%" border=0>
				<TR>
					<TD align=middle colSpan=2 class="ptd13"><B><U>CLIENT INFO</U></B><BR></TD>
				</TR>
				<TR>
					<TD width="35%" class="ptd12" nowrap><B>Client Name:</B>&nbsp;</TD>
					<TD width="65%" style="FONT: 18px arial,verdana,tahoma"><B>{$data.order.name}</B></TD>
				</TR>
				<TR>
					<TD class="ptd12" nowrap><B>Address:</B>&nbsp;</TD>
					<TD style="FONT: 18px arial,verdana,tahoma">{$data.order.address}</TD>
				</TR>
				<TR>
					<TD class="ptd12"><B>Town:</B>&nbsp;</TD>
					<TD style="FONT: 18px arial,verdana,tahoma">{$data.order.town}</TD>
				</TR>
				<TR>
					<TD class="ptd12"><B>State:</B>&nbsp;</TD>
					<TD style="FONT: 16px arial,verdana,tahoma">{$data.order.state}</TD>
				</TR>
				<TR>
					<TD class="ptd12"><B>Zip:</B>&nbsp;</TD>
					<TD style="FONT: 16px arial,verdana,tahoma">{$data.order.zip}</TD>
				</TR>
				{if $data.order.is_contractor}
				<TR>
					<TD class="ptd12" nowrap><B>Company Name:</B>&nbsp;</TD>
					<TD class="ptd12"><font color="red"><b>{$data.order.company_name}</b></font></TD>
				</TR>

				{/if}
				{*
				<TR>
					<TD class="ptd12" nowrap><B>Company Phone:</B>&nbsp;</TD>
					<TD>{if trim($data.order.a_phone)!=''}{$data.order.a_phone}{else}-{/if}</TD>
				</TR>
				<TR>
					<TD class="ptd12" nowrap><B>Home Phone:</B>&nbsp;</TD>
					<TD style="FONT: 16px arial,verdana,tahoma">{$data.order.phone}</TD>
				</TR>
				<TR>
					<TD class="ptd12" nowrap><B>Work Phone:</B>&nbsp;</TD>
					<TD style="FONT: 16px arial,verdana,tahoma">{$data.order.w_phone}</TD>
				</TR>
				<TR>
					<TD class="ptd12" nowrap><B>CellPhone:</B>&nbsp;</TD>
					<TD style="FONT: 16px arial,verdana,tahoma">{$data.order.cell}</TD>
				</TR>*}
				
			</TABLE>
            {else}
            
            {/if}
		</TD>
		<TD valign="top" width="53%">
			<TABLE borderColor="#ffffff" cellSpacing=2 cellPadding=1 width="100%" border=1>
				<TR>
					<TD align=center colSpan=2 class="ptd13"><B><U>UNIT INFO</U></B><BR></TD>
				</TR>
				<TR>
					<TD width="38%" class="ptd12"><B>Unit Name:&nbsp;</B></TD>
					<TD borderColor="#000000" width="62%" class="ptd12">&nbsp;{$data.unit[unt].name} (Id={$data.unit[unt].id})</TD>
				</TR>
				<TR>
					<TD class="ptd12"><B>Application Name:</B>&nbsp;</TD>
					<TD borderColor="#000000" class="ptd12">&nbsp;{$data.unit[unt].app_name}</TD>
				</TR>
				{if is_array($data.unit[unt].cutout) && count($data.unit[unt].cutout)>0}
				{section name=st loop=$data.unit[unt].cutout}
				<TR>
					<TD class="ptd12"><B>Client Cutout Type:</B>&nbsp;</TD>
					<TD borderColor="#000000" class="ptd13">&nbsp;
						{$data.unit[unt].cutout[st].name} ({$data.unit[unt].cutout[st].quantity})<br>
						{if ($data.unit[unt].cutout[st].template)==0}&nbsp;&nbsp;<b><i>Object on Shop</i></b>{/if}
						{if ($data.unit[unt].cutout[st].template)==1}&nbsp;&nbsp;<b><i>Object not on Shop</i></b>{/if}
						{if ($data.unit[unt].cutout[st].template)==2}&nbsp;&nbsp;<b><i>Template on Shop</i></b>{/if}
						{if ($data.unit[unt].cutout[st].template)==3}&nbsp;&nbsp;<b><i>Drop-in Measurements</i></b>{/if}
					</TD>
				</TR>
				{/section}
				{/if}
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
					<TD><SPAN style="FONT: 12px arial,verdana,tahoma"><B>Surface Finish:</B>&nbsp;&nbsp;</SPAN></TD>
					<TD borderColor="#000000">
						<SPAN style="FONT: 13px arial,verdana,tahoma">
						{if $data.unit[unt].honing !=1}
						<INPUT id=rdoSurfaceFinishPolished type="checkbox" {if $data.unit[unt].honing !=1} checked="checked" {/if} value=rdoSurfaceFinishPolished name=rdoSurfaceFinish>
						<LABEL for=rdoSurfaceFinishPolished>Polished</LABEL>&nbsp;&nbsp;&nbsp;
						{else} &nbsp; 
						<INPUT id=rdoSurfaceFinishHoned type="checkbox" {if $data.unit[unt].honing ==1} checked="checked" {/if} value=rdoSurfaceFinishHoned name=rdoSurfaceFinish>
						<LABEL for=rdoSurfaceFinishHoned><strong>HONED</strong></LABEL>
						{/if}
						</SPAN>
					</TD>
				</TR>
				<TR>
					<TD class="ptd12" valign="top"><B>Cut By:</B>&nbsp;&nbsp;</TD>
					<TD borderColor=#000000 class="ptd13">&nbsp;{section name=ct loop=$data.unit[unt].cutter}{if !$smarty.section.ct.first},{/if}{$data.unit[unt].cutter[ct].c_name}{/section}</TD>
				</TR>
				<tr>
					<td>{if $data.unit[unt].inst[0].message==1}{if $data.unit[unt].inst[0].m_voicemail==1}<img src="gfx/task_confirmed_vm.gif" />{else}<img src="gfx/task_confirmed.gif" />{/if}{/if}</td>
				</tr>
			</TABLE>
		</TD>
	</TR>
	<TR>
		<TD colSpan=2 height="1" valign="top" width="100%" bgcolor="#7f7f7f"></TD>
	</TR>
	<TR>
		<TD colSpan=2>
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD nowrap style="FONT: 16px arial,verdana,tahoma">
						<BR><B>Payment Type:&nbsp;</B>{$data.order.pay_level}<BR>
						<div style="FONT: 16px arial,verdana,tahoma">
						{if $data.order.is_contractor}
						<B>Balance:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</B>
						{if $data.order.dep_required}FINAL PAYMENT REQUIRED{/if}
						</div><BR>
					</TD>
						{else}
						{assign var="TotalPayment" value="0"}
						{section name=ck loop=$data.check}
						{assign var="TotalPayment" value=$data.check[ck].total+$TotalPayment}
						{/section}
						{if $no_address == 'false'}<B>Balance:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</B>${$data.order.total_price-$TotalPayment}{/if}
						</div><BR>
					</TD>
						{/if}
				</TR>
			</TABLE>
		</TD>
	</tr>
	<TR>
		<TD colSpan=2 height="1" valign="top" width="100%" bgcolor="#7f7f7f"></TD>
	</TR>
	<TR>
		<TD vAlign=top colSpan=2>
			{if is_array($direction) && is_array($direction.way) && count($direction.way)>0}
			<TABLE cellSpacing=1 cellPadding=1 border=0 width="100%">
				<TR>
					<TD colSpan=2><SPAN style="FONT: 13px arial,verdana,tahoma"><B>Directions:&nbsp;</B></SPAN></TD>
				</TR>
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
					</TD>
				</TR>
			</TABLE><BR>
			{/if}
			<TABLE cellSpacing=1 cellPadding=1 width="100%" border=0>
				<TR>
					<TD>
						{*if is_array($data.log.account) && count($data.log.account)>0}
						<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
							<TR>
								<TD style="FONT: 12px arial,verdana,tahoma" align="left"><B>Account logs:</B></TD>
							</TR>
							<TR>
								<TD vAlign="top" align="right">
									<TABLE style="FONT-SIZE: x-small; WIDTH: 90%; FONT-FAMILY: Arial; BORDER-COLLAPSE: collapse;align: right" cellSpacing="0" border="1">
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
									</TABLE>
								</TD>
							</TR>
						</TABLE>
						{/if*}
						
						{if is_array($data.log.wo) && count($data.log.wo)>0}
						<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
							<TR>
								<TD style="FONT: 12px arial,verdana,tahoma" align="left"><B>Work order logs:</B></TD>
							</TR>
							<TR>
								<TD vAlign="top" align="right">
									<TABLE style="FONT-SIZE: x-small; WIDTH: 90%; FONT-FAMILY: Arial; BORDER-COLLAPSE: collapse;align: right" cellSpacing="0" border="1">
										<TR>
											<TD style="WIDTH: 100px"><b>Created By</b></TD>
											<TD style="WIDTH: 90px"><b>Create Date</b></TD>
											<TD><b>Note</b></TD>
										</TR>
										{section name=lg loop=$data.log.wo}
										{if strpos($data.log.wo[lg].description,"ap...") === false and strpos($data.log.wo[lg].description,"($") === false and strpos($data.log.wo[lg].description,"Transfer Notes") === false}
										<TR>
											<TD style="WIDTH: 100px">{$data.log.wo[lg].w_name}</TD>
											<TD style="WIDTH: 90px">{$data.log.wo[lg].cr_date|date_format:"%D %H:%M"}</TD>
											<TD>{$data.log.wo[lg].description|upper}</TD>
										</TR>
										{/if}
										{/section}
									</TABLE>
								</TD>
							</TR>
						</TABLE>
						{/if}

						{if is_array($data.unit[unt].log) && count($data.unit[unt].log)>0}
						<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
							<TR>
								<TD style="FONT: 12px arial,verdana,tahoma" align="left"><B>WO Unit logs:</B></TD>
							</TR>
							<TR>
								<TD vAlign="top" align="right">
									<TABLE style="FONT-SIZE: x-small; WIDTH: 90%; FONT-FAMILY: Arial; BORDER-COLLAPSE: collapse;align: right" cellSpacing="0" border="1">
										<TR>
											<TD style="WIDTH: 100px"><b>Created By</b></TD>
											<TD style="WIDTH: 90px"><b>Create Date</b></TD>
											<TD><b>Note</b></TD>
										</TR>
										{section name=ulgg loop=$data.unit[unt].log} 
										<TR>
											<TD style="WIDTH: 100px">{$data.unit[unt].log[ulgg].w_name}</TD>
											<TD style="WIDTH: 90px">{$data.unit[unt].log[ulgg].cr_date|date_format:"%D %H:%M"}</TD>
											<TD>{if strpos(strtolower($data.unit[unt].log[ulgg].description),"sink") !== false or strpos(strtolower($data.unit[unt].log[ulgg].description),"*") !== false}
                                              <font size="+2"><b>{$data.unit[unt].log[ulgg].description|upper}</b></font>
                                              {else}
                                              {$data.unit[unt].log[ulgg].description|upper}
                                              {/if}</TD>
										</TR>
										{/section}
									</TABLE>
								</TD>
							</TR>
						</TABLE>
						{/if}
					</TD>
				</TR>
			</TABLE>
		</TD>
	</TR>
</TABLE>

{if $print_release == 'true'}
<div style="page-break-before:always;font-size:1;margin:0;border:0;"><span style="visibility: hidden;">-</span></div>
<center><IMG src="gfx/logo_print.jpg"></center>
<H1 style="font-family:'serif', 'Bookman Old Style';" align="center">Installation Release Form</H1><br>
<P style="text-align:center;"><strong>Installer:</strong> __________________________ <strong>Date:</strong> {section name=ist loop=$data.unit[unt].inst}{if trim($data.unit[unt].inst[ist].inst_date)!=''}{if !$smarty.section.ist.first},{/if}<u>{$data.unit[unt].inst[ist].inst_date|date_format:'%m/%d/%Y'}</u>{/if}{/section}</P>
<P style="text-align:center;"><strong>Work Order #:</strong> <u>{$data.order.id|string_format:"%06.0f"}</u> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Unit #:</strong> <u>{$data.unit[unt].id|string_format:"%06.0f"}</u></P>
<P style="text-align:center;"><strong>Stone Name:</strong> <u>{$data.unit[unt].slab.st_name}&nbsp;&nbsp;{$data.unit[unt].slab.sign} - {$data.unit[unt].slab.thickness}</u></P>
<P style="text-align:center;"><strong>Address:</strong> <u>{$data.order.address}, {$data.order.town}, {$data.order.state} {$data.order.zip}</u></P>
<br>
<br>
<br>
<P style="text-align:justify; text-indent:47px; line-height:15px; padding-left:35px; padding-right:35px;"><SPAN style="font-family:'serif', 'Bookman Old Style'; font-size:9.7pt; font-style:normal; font-weight:normal; color:#000000;">I herby acknowledge, that the installation of my granite countertop was performed professionally, in a workmanlike manner and according to the industry standards. I have made the final inspection of the installed product and I confirm that it is free of misfits and defects (such as cracks, chips, scratches, etc.) All faucet holes are drilled and the countertop is prepared for the attachment of a dishwasher. I acknowledge that All Granite and Marble Corp. does not attach the dishwasher, nor does it offer any plumbing services. I understand that any request for corrections or repairs to the counter submitted after the installation will be treated as a service call and will be charged to my account.</SPAN></P>
<P style="text-align:justify; text-indent:47px; line-height:15px; padding-left:35px; padding-right:35px;"><SPAN style="font-family:'serif', 'Bookman Old Style'; font-size:9.7pt; font-style:normal; font-weight:normal; color:#000000">I also acknowledge that I have received and understood the instructions regarding the care, maintenance, use and cleaning of this natural product. I acknowledge that natural materials are porous and are susceptible to staining. Materials that are honed (marble and granite) or have a dull surface (such as Pietro Cardosa or Limestone) are even more susceptible to staining. All Granite and Marble Corp. will not be responsible or held liable when certain oils or materials are introduced to the stone or accept responsibility for cleaning, repair or replacement of any product, which has been stained or damaged by improper use, which shall be solely determined by the stone fabricator. In no event shall be All Granite and Marble, its officers, employees, agents, or affiliates be liable for consequential, direct, indirect, incidental or any other damages resulting from the inability to complete the performance of the services contracted.</SPAN></P>
<P style="text-align:left; margin-right:6px; text-indent:47px; line-height:15px; padding-left:35px; padding-right:35px;"><SPAN style="font-family:'serif', 'Bookman Old Style'; font-size:9.7pt; font-style:normal; font-weight:normal; color:#000000">All Granite and Marble cannot be responsible for natural voids, chips and dents that are inherent to stone fabrication.</SPAN></P>
<P style="text-align:left; margin-bottom:36px; line-height:15px; padding-left:35px; padding-right:35px;"><SPAN style="font-family:'serif', 'Bookman Old Style'; font-size:9.7pt; font-style:normal; font-weight:normal; color:#000000">I have read, understand and agree to the above information.</SPAN></P>
<P style="text-align:left; margin-bottom:36px; line-height:15px; padding-left:35px; padding-right:35px;"><SPAN style="font-family:'serif', 'Bookman Old Style'; font-size:10.6pt; font-style:normal; font-weight:normal; color:#000000">Name (Please PRINT) ___________________________________________ Date __________________ </SPAN></P>
<P style="text-align:justify; margin-bottom:22px; padding-left:35px; padding-right:35px;"><SPAN style="font-family:'serif', 'Bookman Old Style'; font-size:10.6pt; font-style:normal; font-weight:normal; color:#000000">Signature </SPAN>________________________________________</P>
<P style="text-align:center">{$loc.address}, {$loc.town}, {$loc.state} {$loc.zip}<br />Tel: {$loc.phone} Fax: {$loc.fax}</P>
{/if}

<!--order drawings-->
{assign var="page_cnt" value=$data.unit[unt].drawing_cnt}
{assign var="act_page" value=1}
{if $smarty.section.unt.first}
{capture assign="page_cnt"}{math equation="x+y" x=$page_cnt y=$data.order.drawing_cnt}{/capture}
{section name=odraw loop=$data.order.drawing}
<div style="page-break-before:always;font-size:1;margin:0;border:0;"><span style="visibility: hidden;">-</span></div>
<TABLE cellSpacing=1 cellPadding=1 width="670" align=center bgColor="#000000" border=0>
	<TR>
		<TD bgColor="#ffffff">
			<TABLE cellSpacing=2 cellPadding=2 width="100%" align=center border=0>
				<TR>
					<TD align=left style="FONT: 30px arial,verdana,tahoma">
						<B>Work Order Id:&nbsp;</B>{$data.order.id|string_format:"%06.0f"}&nbsp;&nbsp;&nbsp;
						<div style="FONT: 16px arial,verdana,tahoma"><B>Unit Id:&nbsp;</B>{$data.unit[unt].id|string_format:"%06.0f"}</div>
					</TD>
					<TD align=right style="FONT: 16px arial,verdana,tahoma"><I>{$act_page} of {$page_cnt}{capture assign="act_page"}{math equation="x+1" x=$act_page}{/capture}</I></TD>
				</TR>
			</TABLE>
		</TD>
	</TR>
	<tr>
		<td height="750" valign="top" bgColor="#ffffff">
			<table width="100%" valign="top" align="center">
				<tr>
					<td colspan="2" height="4"></td>
				</tr>
				<TR>
					<td colspan="2"><img src="upload/order/{$data.order.drawing[odraw].id}_{$data.order.drawing[odraw].picture}" width="{$data.order.drawing[odraw].w}" height="{$data.order.drawing[odraw].h}" border="0"></td>
				</TR>
			</table>
		</td>
	</tr>
</TABLE>
{/section}
{/if}
<!--order drawings end-->

<!--order pictures-->
{if $smarty.section.unt.first && $pictures == 'true'}
{section name=opics loop=$data.order.pictures}
{capture assign="page_cnt2"}{math equation="ceil(x/4)" x=$smarty.section.opics.total}{/capture}
{if $smarty.section.opics.index % 4 == 0}
<div style="page-break-before:always;font-size:1;margin:0;border:0;"><span style="visibility: hidden;">-</span></div>
<div style="writing-mode: tb-rl; height:100%">
	<B>Work Order Id:&nbsp;{$data.order.id} <I>{$act_page} of {$page_cnt2}{capture assign="act_page"}{math equation="x+1" x=$act_page}{/capture}</I><br />
{/if}
	<img src="upload/ord_picture/small/{$data.order.pictures[opics].id}_{$data.order.pictures[opics].picture}" {if $data.order.pictures[opics].format == 'l'}vspace="54" hspace="2"{else}vspace="54" hspace="112"{/if} border="0" {if $data.order.pictures[opics].format == 'l'}style="Filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=1)"{/if}>
	{if $smarty.section.opics.iteration % 2 == 0 && ($smarty.section.opics.iteration % 4 != 0 || $smarty.section.opics.total <= 4)}
	<br />
	{/if}
{if ($smarty.section.opics.iteration % 4 == 0 && !$smarty.section.opics.first) || $smarty.section.opics.last}
</div>
{/if}
{/section}
{/if}
<!--order pictures end-->

<!--unit drawings-->
{section name=udraw loop=$data.unit[unt].drawing}
<div style="page-break-before:always;font-size:1;margin:0;border:0;"><span style="visibility: hidden;">-</span></div>
<TABLE cellSpacing=1 cellPadding=1 width="670" align=center bgColor="#000000" border=0>
	<TR>
		<TD bgColor="#ffffff">
			<TABLE cellSpacing=2 cellPadding=2 width="100%" align=center border=0>
				<TR>
					<TD align=left style="FONT: 30px arial,verdana,tahoma">
						<B>Work Order Id:&nbsp;</B>{$data.order.id|string_format:"%06.0f"}&nbsp;&nbsp;&nbsp;
						<div style="FONT: 16px arial,verdana,tahoma"><B>Unit Id:&nbsp;</B>{$data.unit[unt].id|string_format:"%06.0f"}&nbsp;&nbsp;&nbsp;<B>Name:&nbsp;</B>{$data.unit[unt].name}</div>
					</TD>
					<TD align=right style="FONT: 16px arial,verdana,tahoma"><I>{$act_page} of {$page_cnt}{capture assign="act_page"}{math equation="x+1" x=$act_page}{/capture}</I></TD>
				</TR>
			</TABLE>
		</TD>
	</TR>
	<tr>
		<td height="750" valign="top" bgColor="#ffffff">
			<table width="100%" valign="top" align="center">
				<tr>
					<td colspan="2" height="4"></td>
				</tr>
				<TR>
					<td colspan="2" align="center"><img src="upload/ord_unit/{$data.unit[unt].drawing[udraw].id}_{$data.unit[unt].drawing[udraw].picture}" width="{$data.unit[unt].drawing[udraw].w}" height="{$data.unit[unt].drawing[udraw].h}" border="0"></td>
				</TR>
			</table>
		</td>
	</tr>
</TABLE>
{/section}
<!--unit drawings end-->