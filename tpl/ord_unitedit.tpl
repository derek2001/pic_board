{include file="header.tpl"}
<script type="text/javascript" src="js/design.js"></script>
<script language="javascript">
var disclaimer=1; var picture=0; 
{literal}
 function checkForm()
 {
  if (document.edit['edit[name]'].value=='' ||
      document.edit['edit[cust_price]'].value=='' ||
      document.edit['edit[slab_nbr]'].value=='') {alert('Fields marked with * must be set.'); return false;}

  if (document.edit['edit[id_application]'].value=='') {alert('Application is required.'); return false;}
  document.edit['edit[id_application]'].disabled = false;
  document.edit['edit[id_edge]'].disabled = false;
  document.edit['edit[honing]'].disabled = false;
  document.edit['edit[is_rectangle]'].disabled = false;
  if (typeof(document.edit['edit[id_slab_frame]'])=='object') document.edit['edit[id_slab_frame]'].disabled = false;
  var custom = parseFloat(document.edit['edit[cust_price]'].value);
  if (isNaN(custom)) {alert('Custom must be numeric.'); return false;}
  if (custom>0)
  {
    var description = document.edit['edit[cust_description]'].value;
    while(description.length > 0 && description.search(' ')>=0)  description = description.replace(' ','')
    if (description=='') {alert('Custom description is required, when custom price is set.'); return false;}
  }
  return true;
 }
 function onlydec(event,f)
 {
  if (event.srcElement) {kc =  event.keyCode;} else {kc =  event.which;}
  if ((kc < 47 || kc > 57) && kc != 8 && kc != 0) return false;
  return true;
 }
  function decimalWrite(event,field,lgth)
{
  //lgth - liczba miejsc po przecinku
  if (event.srcElement) {kc =  event.keyCode;} else {kc =  event.which;}
  var point=document.getElementById(field).value.indexOf('.');
  if ((kc != 8 && kc != 0) || point>0)
  {
   if ((kc < 48 || kc > 57) && kc!=46) return false;
   if (point>0)
    if (kc==46) return false;
     else if (document.getElementById(field).value.length-point>lgth) return false;
  }
  return true;
}
 function sub(a)
 {
  if(a==1)
   document.edit['act'].value = 'Saving';
  if(a==2) 
   document.edit['act'].value = 'Sample';
  document.edit.submit();
 }
 function reload()
 {
   var g=document.edit['edit[id_application]'].value;
   //window.alert(g);
   spis.location="app_question.php?id="+g;
 }
 function changeDisclaimer(obj)
 {
   if (disclaimer==0) { disclaimer = 1; obj.alt = "Print with disclaimers"; }
     else { disclaimer = 0; obj.alt = "Print without disclaimers"; }
   obj.src='gfx/pr_disc_'+disclaimer+'.gif';
 }
 function changePicture(obj)
 {
   if (picture==0) { picture = 1; obj.alt = "Print with drawings"; }
     else { picture = 0; obj.alt = "Print without drawings"; }
   obj.src='gfx/pr_pict_'+picture+'.gif';
 }



{/literal}
</script>
{if $uid<>''}{assign var="module_name" value="Order unit"}{else}{assign var="module_name" value="Create new order unit"}{/if}
{include file="module_header.tpl"}
{if $error<>''}<b><font color="red">{$error}</font></b><br>{/if}
<center>
<table width="1222" border="0">
<form action="ord_unitedit.php" method="post" name="edit" onsubmit="return checkForm();"><input type="hidden" name="alastid" value="{$alastid}">
<input type="hidden" name="uid" value="{$uid}"><input type="hidden" name="oid" value="{$oid}"><input type="hidden" name="id_account" value="{$id_account}">
<input type="hidden" name="edit[old_id_slab_frame]" value="{if !is_numeric($ord_unit.id_slab_frame)}NULL{else}{$ord_unit.id_slab_frame}{/if}">
<input type="hidden" name="edit[old_cust_description]" value="{$ord_unit.cust_description}">
<input type="hidden" name="edit[old_id_edge]" value="{$ord_unit.id_edge}" />
 <tr >
 {if $uid eq ''}<td  valign="top" width="30%">&nbsp;</td>{/if}
 <td rowspan="3"valign="top" width="35%">
 {assign var="table_width" value="370"}
 {assign var="table_headertitle" value="Unit info:"}
 {include file="search_header.tpl"}
  {if $uid<>''}
   <tr class="cell_reccolor_lightblue_01a">
    <td>ID:</td><td>{$uid} {if $wo_unit_print}&nbsp;<a href="#" onClick="window.open('print_wounit.php?oid={$oid}&uid={$uid}&pict='+picture+'&disc='+disclaimer,'print_order','toolbar=1,menu=1,scrollbars=1,resizable=1');"><img src="gfx/printer.gif" border="0" alt="Print unit price.{if $can_always_print && !$can_print}Warning: no deposit on that order or invoice no. was not generated!{/if}"></a>
       <img src="gfx/pr_disc_1.gif" style="cursor:hand" border="0" alt="Print with disclaimers" onclick="changeDisclaimer(this)">&nbsp;
       <img src="gfx/pr_pict_0.gif" style="cursor:hand" border="0" alt="Print without drawings" onclick="changePicture(this)">
       <a href= "#"  onclick="window.open('acc_inf.php?oid={$oid}','acc_info','toolbar=0,menu=0,left=100,top=30,scrollbars=0,resizable=0, status=0,width=500,height=420' );">
       <img src="gfx/show1.gif" onmouseover="this.src='gfx/show0.gif'" onmouseout="this.src='gfx/show1.gif'"style="cursor:hand" border="0" alt="Account information" ></a>
	   </td>
   </tr>
  {/if}
  {/if}
   <tr class="cell_reccolor_lightblue_01b">
    <td>Name:*</td>
    <td><input type="text" value="{$ord_unit.name}" name="edit[name]" size="20" maxlength="50"{$lock_form}></td>
   </tr>
   <tr class="cell_reccolor_lightblue_01a">
    <td>Application:*</td>
    <td>
     {if $lock_select==''}
            <select name="edit[id_application]" onChange="reload();">
                   <option value=''></option>
                   {section name=d loop=$application}
                   <option value="{$application[d].id}" {if $ord_unit.id_application eq $application[d].id}selected{/if}>{$application[d].name}
                   {/section}
            </select>
     {else}
            {section name=d loop=$application}
                   {if $ord_unit.id_application eq $application[d].id}{$application[d].name}{/if}
               {/section}
            <input type="hidden" name="edit[id_application]" value="{$ord_unit.id_application}">
     {/if}
    </td>
   </tr>
   <tr class="cell_reccolor_lightblue_01b">
    <td>Default edge:</td>
    <td>
     {if $lock_select==''}
            <select name="edit[id_edge]">
                   <option value=''></option>
                   {section name=ed loop=$edge}
                   <option value="{$edge[ed].id}" {if $uid<>''}{if $ord_unit.id_edge eq $edge[ed].id}selected{/if}{else}{if $edge[ed].id == 20}selected{/if}{/if}>{$edge[ed].name}
                   {/section}
            </select>
     {else}
            {if $ord_unit.id_edge!='' && $ord_unit.id_edge>0}
            {section name=ed loop=$edge}
                   {if $ord_unit.id_edge eq $edge[ed].id}{$edge[ed].name}{/if}
            {/section}
            {else}-{/if}
            <input type="hidden" name="edit[id_edge]" value="{$ord_unit.id_edge}">
     {/if}
     {if $fragile == 1 || $risky == 1}<font color="red">No CNC</font>{/if}
    </td>
   </tr>
   {if $cnt >0}
   <tr class="cell_reccolor_gold_01a">
    <td>Other units</td>
    <td>
     {if $lock_select==''}
            <select name="edit[id_ord_unit]">
                   <option value="">
                   {section name=dd loop=$other_units}
                   <option value="{$other_units[dd].id}" {if $ord_unit.id_ord_unit eq $other_units[dd].id}selected{/if}>{$other_units[dd].name}
                   {/section}
            </select>
        {else}
            {if $ord_unit.id_ord_unit!='' && $ord_unit.id_ord_unit>0}
            {section name=dd loop=$other_units}
                   {if $ord_unit.id_ord_unit eq $other_units[dd].id}{$other_units[dd].name}{/if}
            {/section}
            {else}-{/if}
            <input type="hidden" name="edit[id_ord_unit]" value="{$ord_unit.id_ord_unit}">
        {/if}
    </td>
   </tr>
   {/if}
   <tr class="cell_reccolor_lightblue_01a">
    <td>Honing:</td>
    <td>
     {if $lock_select==''}
            <select name="edit[honing]" size="1">
               <option value="0" {if $ord_unit.honing eq 0}selected{/if}>No
               <option value="1" {if $ord_unit.honing eq 1}selected{/if}>Yes
            </select>
     {else}
            {if $ord_unit.honing==0}No{else}Yes{/if}
            <input type="hidden" name="edit[honing]" value="{$ord_unit.honing}">
     {/if}
    </td>
   </tr>
   <tr class="cell_reccolor_lightblue_01b">
    <td valign="top">Custom:*</td>
    <td><input type="text" {if $ord_unit.cust_price eq true}value="{$ord_unit.cust_price}"{else}value="0"{/if} name="edit[cust_price]" size="10" maxlength="50" id="paid" onKeyPress="return decimalWrite(event,'paid',2)"{$lock_form}>&nbsp;<b>$</b></td>
   </tr>
   <tr class="cell_reccolor_lightblue_01a">
    <td valign="top">Cust. description:</td>
    <td>
     <textarea name="edit[cust_description]" rows="3" cols="17"{$lock_form}>{$ord_unit.cust_description}</textarea>
    </td>
   </tr>


   {if $uid<>''}
    <tr class="cell_reccolor_gold_01a">
     <td>Selected stone:</td>
     <td><b><div  id="sel_stone">-</div><div id="outside" style="color:red;"> </div><div id="risky" style="color:red;"> </div></b></td>
   </tr>
    <tr class="cell_reccolor_gold_01b">
    <td>Edge:</td>
    <td>{$ord_unit.edge}&nbsp;ft
     <input type="hidden" name="edit[edge]" value="{$ord_unit.edge}">
    </td>
    </tr>
   <tr class="cell_reccolor_gold_01a">
    <td>Area:</td>
    <td>{$ord_unit.area}&nbsp;sqft
     <input type="hidden" name="edit[area]" value="{$ord_unit.area}">
    </td>
   </tr>
   
   <tr class="cell_reccolor_gold_01b">
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Backsplash:</td>
    <td>{$ord_unit.backsplash}&nbsp;ft
     <input type="hidden" name="edit[backsplash]" value="{$ord_unit.backsplash}">
    </td>
   </tr>
   {if $show_wo_price}
   <tr class="cell_reccolor_gold_01a">
    <td>Total price:</td>
    <td>${$ord_unit.tot_price|string_format:"%.2f"}&nbsp;
     <input type="hidden" name="edit[tot_price]" value="{$ord_unit.tot_price}">
    </td>
   </tr>
    {/if}
   {/if}

   <tr class="cell_reccolor_lightblue_01b">
    <td>Rectangle:</td>
    <td>
     {if $lock_select==''}
            <select name="edit[is_rectangle]" size="1">
                    <option value="0" {if $ord_unit.is_rectangle eq 0}selected{/if}>No
                    <option value="1" {if $ord_unit.is_rectangle eq 1}selected{/if}{if $ord_unit.is_rectangle eq false}selected{/if}>Yes
            </select>
     {else}
            {if $ord_unit.is_rectangle==0}No{else}Yes{/if}
            <input type="hidden" name="edit[is_rectangle]" value="{$ord_unit.is_rectangle}">
     {/if}
    </td>
   </tr>
   {if $show_slab_nbr}
   <tr class="cell_reccolor_lightblue_01a">
    <td>Number of slabs:*</td>
    <td><input type="text" {if $ord_unit.slab_nbr!=''}value="{$ord_unit.slab_nbr}"{else}value="1"{/if} name="edit[slab_nbr]" size="10" maxlength="50" readonly></td>
   </tr>
   {else}
   <input type="hidden" {if $ord_unit.slab_nbr!=''}value="{$ord_unit.slab_nbr}"{else}value="1"{/if} name="edit[slab_nbr]">
   {/if}

   {if $uid<>''}
   
   <tr class="cell_reccolor_lightblue_01b">
    <td><div>Salesman:</div></td>
    <td><div>{$name.fname}&nbsp;{$name.lname}</div></td>
   </tr>
   {/if}
   
   <input type="hidden" name="edit[ord_slab_id]" value="{$ord_unit.ord_slab_id}">
   <tr><td colspan="2"><table width="100%" cellspacing="0" cellpadding="2" style="border: 1px solid black;">
      <tr class="cell_reccolor_blue_01b">
       <td>Stone search:</td>
       <td><input type="text" name="edit[string]" value="{$ord_unit.string}"{$lock_form}>&nbsp;<input type="button" class="BUTTON_OK" value="Search" onClick="sub(1);"{$lock_select}></td>
      </tr>
      <tr class="cell_reccolor_blue_01a">
      {if $f_cnt>0}
       <td colspan="2" align="center">
        <select name="edit[id_slab_frame]" id="id_slab_frame" style="width: 350px"{$lock_select}>
         <option value="NULL">NONE</option>
         {section name=xx loop=$slabs}
         {if $ord_unit.id_slab_frame eq $slabs[xx].sf_id && $slabs[xx].sl_outside == 0}<script>document.getElementById('outside').innerHTML = 'Do not use this stone outside.';</script>{/if}
         {if $ord_unit.id_slab_frame eq $slabs[xx].sf_id && $slabs[xx].sl_risky == 1}<script>document.getElementById('risky').innerHTML = 'Stone may stain or damage during production.';</script>{/if}
         <option value="{$slabs[xx].sf_id}" {if $ord_unit.id_slab_frame eq $slabs[xx].sf_id}selected{/if}>{$slabs[xx].st_name}-{$slabs[xx].st_id}-{$slabs[xx].f_sign}-{$slabs[xx].sl_thick}-{$slabs[xx].sl_widht}x{$slabs[xx].sl_length}-{if ($slabs[xx].sc_name)==null}Super Premium {else}{$slabs[xx].sc_name}{/if}
         {/section}
        </select>
       </td>
      {else}
       <td colspan="2"><div align="center"><font color="red"><b>No search result</b></font></div></td>
      {/if}
      </tr>
   </table></td></tr>
 {include file="search_footer.tpl"}
 <center>
      <input type="hidden" name="act" value="edit">
      <input type="button" name="cancel" value="Go Back" class="BUTTON_CANCEL" onClick="location.href='orderedit.php?id={$oid}&id_account={$id_account}'">
      <input type="submit" value="Save" class="BUTTON_OK" {if $f_cnt==0 or $wo_unit_lock==true}disabled{/if}>
 </center>
 
 {if is_array($e_forms)}
 <br /><br />
 {assign var="table_width" value="370"}
  {assign var="table_headertitle" value="Electronic Forms:"}
  {include file="search_header.tpl"}
   <tr class="cell_reccolor_neutral_01">
    <td width="200">
     Form
    </td>
	<td width="85">
     Signature
    </td>
	<td width="85">
     Date Signed
    </td>
   </tr>
   {section name=e loop=$e_forms}
   <tr class="cell_reccolor_blue_01a">
    <td width="200">
	<script>
	var szer = document.body.clientWidth;
	if(szer > 1800) szer = 1921;
	else if(szer > 1280) szer = 1601;
	else szer = 1281;
	</script>
     {if $e_forms[e].signed != 1}<a href="#" onClick="javascript:window.open('e_forms.php?oid={$oid}&fid={$e_forms[e].id}','print_eform','height=740,width=1024,left='+szer+',top=0,toolbar=0,menu=0,scrollbars=0,resizable=0');">{$e_forms[e].name}</a>{else}
	 <a href="#" onClick="javascript:window.open('e_forms.php?oid={$oid}&fid={$e_forms[e].id}','print_eform','height=880,width=750,toolbar=1,menu=1,scrollbars=1,resizable=0');">{$e_forms[e].name}</a>{/if}
    </td>
	<td width="85">
     {if $e_forms[e].signed == 1}<font color="#FF0000">Yes</font>{else}No{/if}
    </td>
	<td width="85">
     {$e_forms[e].date_signed|date_format:'%m/%d/%Y'}</td>
   </tr>
   {/section}
  {include file="search_footer.tpl"} 
 {/if}
 </td><!--koniec pierwszej kolumny-->
 
 {if $ord_unit.id_slab_frame!='' && $ord_unit.id_slab_frame>0}
 <!-- Load selected stone -->
 <script language="javascript">
    document.getElementById('sel_stone').innerHTML = document.getElementById('id_slab_frame').options[document.getElementById('id_slab_frame').selectedIndex].text;
 </script>
 {/if}
 
 <td valign="top" width="30%"><!--poczatek drugiej kolumny-->
 {if $uid<>''}
  {assign var="table_width" value="355"}
  {assign var="table_headertitle" value="Unit status<span id=\"ustatus_act\"></span>:"}
  {include file="search_header.tpl"}
   <tr class="cell_reccolor_neutral_01">
    <td width="100%">
    {$stats}
    </td>
   </tr>
  {include file="search_footer.tpl"}
  </form>
  {/if}
  {assign var="table_width" value="260"}
  {assign var="table_headertitle" value="Application questions:"}
  {include file="search_header.tpl"}
   <tr class="cell_reccolor_neutral_01">
    <td width="100%">
     <iframe height="120" width="350" name="spis" frameborder="0" {if $ord_unit.id_application}src="app_question.php?id={$ord_unit.id_application}"{else}src="app_question.php"{/if}>twoja przegladarka nie obsluguje ramek</iframe>
    </td>
   </tr>
  {include file="search_footer.tpl"}
 </td><!--koniec trzeciej kolumny-->
 
 <td valign="top" width="35%"><!--druga kolumna-->
 {if is_array($crrectangles) && count($crrectangles)>0}
  {assign var="table_width" value="335"}
  {assign var="rec_cnt" value=$crrectangles_cnt}
  {assign var="table_headertitle" value="Rectangles </u> (<a href=\"element_rectedit.php?uid=$uid&oid=$oid&ida=$id_account\">$rec_cnt</a>):
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Elements:($rsq)"}

  {if $unit_edit}{assign var="table_link" value="element_rectedit.php?uid=$uid&oid=$oid&ida=$id_account"}{/if}
  {include file="table_header.tpl"}
         <tr class="cell_reccolor_neutral_01">
           <td width="8%">ID</td>
           <td width="14%">Height</td>
           <td width="14%">Length</td>
           <td width="15%">SQFT</td>
           <td width="10%">E</td>
           <td width="10%">B</b></td>
           <td width="10%">Q</b></td>

          <td width="20%"></td>
         </tr>
         {section name=unt loop=$crrectangles}
         {assign var="_i_" value=$smarty.section.unt.index}
          <tr class="{if $_i_ is even}cell_reccolor_green_01a{else}cell_reccolor_green_01b{/if}">
           <td  >&nbsp;{$crrectangles[unt].id}</td>
           <td  >&nbsp;{$crrectangles[unt].height}</td>
           <td  >&nbsp;{$crrectangles[unt].length}</td>
           <td  >&nbsp;{math equation="x *y/144"  x=$crrectangles[unt].height y=$crrectangles[unt].length a=$crrectangles[unt].quantity format="%.2f"}</td>
           <td >&nbsp;{math equation="x "  x=$crrectangles[unt].edge a=$crrectangles[unt].quantity format="%.2f"}</td>
           <td  >&nbsp;{math equation="x "  x=$crrectangles[unt].bspl a=$crrectangles[unt].quantity format="%.2f"}</td>
           <td  >&nbsp;{$crrectangles[unt].quantity}</td>
           <td ><a href="element_rectedit.php?id={$crrectangles[unt].id}&uid={$uid}&oid={$oid}&ida={$id_account}"><img src="gfx/edit_one1.gif" onmouseover="this.src='gfx/edit_one0.gif'" onmouseout="this.src='gfx/edit_one1.gif'" alt="Edit rectangle '{$crrectangles[unt].id}'" border="0"></a>
             {if $delete eq true && $unit_edit}<a href="ord_unitedit.php?delete=1&id={$crrectangles[unt].id}&uid={$uid}&oid={$oid}&ida={$id_account}" onclick="return askme('Rectangle \'{$data[dat].id}\'')"><img src="gfx/trash1.gif" onmouseover="this.src='gfx/trash0.gif'" onmouseout="this.src='gfx/trash1.gif'" alt="Remove '{$data[dat].id}'" border="0"></a>&nbsp;{/if}
            </td>    
         </tr>
         {/section}
  {include file="table_footer.tpl"}
  <br>
 {/if}
   {if is_array($crshapes) && count($crshapes)>0}
    {assign var="table_width" value="335"}
    {assign var="table_link" value="ord_shapeedit.php"}
    {assign var="table_headertitle" value="Custom charge ($crshapes_cnt):"}
    {include file="table_header.tpl"}
       <tr class="cell_reccolor_neutral_01">
         <td width="10%">ID</td>
         <td width="35%">Name</td>
         <td width="15%">Price</td>
         <td width="30%">Measure</td>
         <td width="10%">&nbsp;</td>
       </tr>
       {section name=est loop=$crshapes}
       {assign var="_i_" value=$smarty.section.est.index}
        <tr class="{if $_i_ is even}cell_reccolor_red_01a{else}cell_reccolor_red_01b{/if}">
         <td width="10%" valign="top">{$crshapes[est].id}</td>
         <td width="35%" valign="top">{$crshapes[est].name}</td>
         <td width="15%" valign="top">${math equation="x * y" x=$crshapes[est].price y=$crshapes[est].shape_nbr format="%.2f"}</td>
         <td width="30%" valign="top">{$crshapes[est].unit}: {$crshapes[est].shape_nbr|string_format:'%.2f'}</td>
         <td width="10%"><a href="ord_shapeedit.php?id_account={$id_account}&oid={$oid}&uid={$uid}&edd={$crshapes[est].id}"><img src="gfx/edit_one1.gif" onmouseover="this.src='gfx/edit_one0.gif'" onmouseout="this.src='gfx/edit_one1.gif'" alt="Edit custom charge '{$crshapes[est].name}'" border="0"></a></td>
       </tr>
       {/section}
    {include file="table_footer.tpl"}
    <br>
   {/if}
 {if is_array($crcutouts) && count($crcutouts)>0}
  {assign var="table_width" value="335"}
  {assign var="cut_cnt" value=$crcutouts_cnt}
  {if $unit_edit}{assign var="table_link" value="ord_cutoutedit.php?uid=$uid&oid=$oid&id_account=$id_account"}{/if}
  {assign var="table_headertitle" value="Unit cutouts ($cut_cnt): &nbsp; &nbsp; &nbsp; 
  Elements:($scut)"}
  {include file="table_header.tpl"}
         <tr class="cell_reccolor_neutral_01">
           <td width="7%">ID</td>
           <td width="40%">Name</td>
           <td width="5%">Q</td>
           <td width="35%">Cr. date</td>
           <td width="10%"></td>
         </tr>
         {section name=ord loop=$crcutouts}
         {assign var="_i_" value=$smarty.section.ord.index}
         <tr class="{if $_i_ is even}cell_reccolor_blue_01a{else}cell_reccolor_blue_01b{/if}">
          <td valign="middle">&nbsp; {$crcutouts[ord].id}</td>
          <td valign="middle">&nbsp;{$crcutouts[ord].name}</td>
          <td valign="middle">&nbsp;{$crcutouts[ord].quantity}</td>
          <td valign="middle">&nbsp;{$crcutouts[ord].cr_date|date_format:'%m/%d/%Y'}</td>
          <td valign="middle"><a href="ord_cutoutedit.php?cutid={$crcutouts[ord].id}&id_account={$id_account}&oid={$oid}&uid={$uid}"><img src="gfx/edit_one1.gif" onmouseover="this.src='gfx/edit_one0.gif'" onmouseout="this.src='gfx/edit_one1.gif'" alt="Edit cutout '{$crcutouts[ord].id}'" border="0"></a></td>
         </tr>
         {/section}
  {include file="table_footer.tpl"}
  <br>
 {/if}
 {if is_array($crcutters) && count($crcutters)>0}
  {assign var="table_width" value="335"}
  {assign var="cutt_cnt" value=$crcutters_cnt}
  {if $unit_edit}{assign var="table_link" value="ord_cutteredit.php?uid=$uid&oid=$oid&id_account=$id_account"}{/if}
  {assign var="table_headertitle" value="Unit cutters ($cutt_cnt):"}
  {include file="table_header.tpl"}
         <tr class="cell_reccolor_neutral_01">
           <td width="15%">ID</td>
           <td width="40%">Name</td>
           <td width="35%">Cr. date</td>
           <td width="10%"></td>
         </tr>
         {section name=ord loop=$crcutters}
         {assign var="_i_" value=$smarty.section.ord.index}
         <tr class="{if $_i_ is even}cell_reccolor_blue_01a{else}cell_reccolor_blue_01b{/if}">
          <td width="15%" valign="top">{$crcutters[ord].id}</td>
          <td width="40%" valign="top">{$crcutters[ord].fname} {$crcutters[ord].lname}</td>
          <td width="35%" valign="top">{$crcutters[ord].c_date|date_format:'%m/%d/%Y'}</td>
          <td width="10%" valign="top">
          {if $cutt_edit}
           <a href="ord_cutteredit.php?id={$crcutters[ord].id}&id_account={$id_account}&oid={$oid}&uid={$uid}">
           <img src="gfx/edit_one1.gif" onmouseover="this.src='gfx/edit_one0.gif'" onmouseout="this.src='gfx/edit_one1.gif'" alt="Edit cutter '{$crcutters[ord].lname}'" border="0"></a>
          {/if}
          </td>
         </tr>
         {/section}
  {include file="table_footer.tpl"}
  <br>
 {/if}
 {if is_array($crunits) && count($crunits)>0}
  {assign var="table_width" value="335"}
  {assign var="cun_cnt" value=$crunits_cnt}
  {if $unit_edit}{assign var="table_link" value="ord_unitedit.php?oid=$oid&id_account=$id_account"}{/if}
  {assign var="table_headertitle" value="Other units ($cun_cnt):"}
  {include file="table_header.tpl"}
         <tr class="cell_reccolor_neutral_01">
           <td width="15%">ID</td>
           <td width="40%">Name</td>
           <td width="35%">Cr. date</td>
           <td width="10%"></td>
         </tr>
         {section name=ord loop=$crunits}
         {assign var="_i_" value=$smarty.section.ord.index}
         <tr class="{if $_i_ is even}cell_reccolor_grey_01a{else}cell_reccolor_grey_01b{/if}">
           <td width="15%" valign="top">{$crunits[ord].id}</td>
           <td width="40%" valign="top">{$crunits[ord].name}</td>
           <td width="35%" valign="top">{$crunits[ord].cr_date|date_format:'%m/%d/%Y'}</td>
           <td width="10%" valign="top"><a href="ord_unitedit.php?uid={$crunits[ord].id}&id_account={$id_account}&oid={$oid}"><img src="gfx/edit_one1.gif" onmouseover="this.src='gfx/edit_one0.gif'" onmouseout="this.src='gfx/edit_one1.gif'" alt="Edit unit '{$crunits[ord].id}'" border="0"></a></td>
         </tr>
         {/section}
  {include file="table_footer.tpl"}
  <br>
 {/if}


 {if is_array($crtemplate) && count($crtemplate)>0}
  {assign var="table_width" value="335"}
  {assign var="table_headertitle" value="Unit template:"}
  {assign var="table_link" value=""}
  {include file="table_header.tpl"}
         <tr class="cell_reccolor_neutral_01">
           <td width="15%">ID</td>
           <td width="40%">Templator</td>
           <td width="35%">Temp. date</td>
           <td width="10%"></td>
         </tr>
         <tr class="cell_reccolor_blue_01a">
           <td width="15%" valign="top">{$crtemplate[0].id}</td>
           <td width="40%" valign="top">{$crtemplate[0].fname} {$crtemplate[0].lname}</td>
           <td width="35%" valign="top">{$crtemplate[0].temp_date|date_format:'%m/%d/%Y'}</td>
           <td width="10%" valign="top"><a href="ord_templateedit.php?id={$crtemplate[0].id}&id_account={$id_account}&oid={$oid}&uid={$uid}"><img src="gfx/edit_one1.gif" onmouseover="this.src='gfx/edit_one0.gif'" onmouseout="this.src='gfx/edit_one1.gif'" alt="Edit template '{$crtemplate[0].id}'" border="0"></a></td>
         </tr>
  {include file="table_footer.tpl"}
  <br>
 {/if}
 {if is_array($crinstall) && count($crinstall)>0}
  {assign var="table_width" value="335"}
  {assign var="table_headertitle" value="Unit installation:"}
  {assign var="table_link" value=""}
  {include file="table_header.tpl"}
         <tr class="cell_reccolor_neutral_01">
           <td width="15%">ID</td>
           <td width="40%">Instalator</td>
           <td width="35%">Inst. date</td>
           <td width="10%"></td>
         </tr>
         <tr class="cell_reccolor_green_01a">
           <td width="15%" valign="top">{$crinstall[0].id}</td>
           <td width="40%" valign="top">{$crinstall[0].fname} {$crinstall[0].lname}</td>
           <td width="35%" valign="top">{$crinstall[0].inst_date|date_format:'%m/%d/%Y'}</td>
           <td width="10%" valign="top"><a href="ord_installationedit.php?id={$crinstall[0].id}&uid={$uid}&id_account={$id_account}&oid={$oid}"><img src="gfx/edit_one1.gif" onmouseover="this.src='gfx/edit_one0.gif'" onmouseout="this.src='gfx/edit_one1.gif'" alt="Edit installation '{$crinstall[0].id}'" border="0"></a></td>
         </tr>
  {include file="table_footer.tpl"}
  
 {/if}
 </td><!--koniec drugiej-->

 <tr><td align="left" colspan="2">
{if $uid <>''}

 <table>
  {if $printouts}
  <tr><td align="left">
         {assign var="table_headertitle" value="Printouts:"}
         {assign var="table_width" value="750"}
       {include file="table_header.tpl"}
       <tr><td >
                <table><tr class="cell_reccolor_neutral_01">
                              <td width="20%"><a href="#" onClick="javascript:window.open('print_template.php?oid={$oid}&uid={$uid}','print_order','toolbar=1,menu=1,scrollbars=1,resizable=1');">Template</a></td>
                              <td width="20%"><a href="#" onClick="javascript:window.open('print_install.php?oid={$oid}&uid={$uid}&no_address=false','print_order','toolbar=1,menu=1,scrollbars=1,resizable=1');">Installation</a></td>
                              <td width="20%"><a href="#" onClick="javascript:window.open('print_production.php?oid={$oid}&uid={$uid}','print_order','toolbar=1,menu=1,scrollbars=1,resizable=1');">Production</a></td>
                              <td width="20%"><a href="#" onClick="javascript:window.open('upload/form/Natural_Stone_Disclaimer.pdf','print_order','toolbar=1,menu=1,scrollbars=1,resizable=1');">Natural Stone Disclaimer</a></td>
							  <td width="20%"><a href="#" onClick="javascript:window.open('upload/form/Drawing_Template{$ord_unit.id_location}.pdf','print_order','toolbar=1,menu=1,scrollbars=1,resizable=1');">Drawing Template</a></td>
                </tr></table>
       </td></tr>
       {include file="table_footer.tpl"}
  </td></tr>
  {/if}
  <tr><td>{$pict}</td></tr><tr><td>{$log}</td></tr></table></center>



</tr>

</td>








 </tr>
 
 <tr>
  <td></td>
  <td colspan="2">
  <center>
  </td>
 </tr>
 {/if}
 <tr>
 {if $uid eq ''}
 <td>&nbsp;</td>
 {/if}
  <td align="right" valign="top">
  </td>
  {if $uid <>''}
  </td>&nbsp;<td>
  {/if}
  <td>&nbsp;</td>
 </tr>
</table>
</center>
{include file="foot.tpl"}
