{include file="header.tpl"}
{dhtml_calendar_init src='js/jscalendar/calendar.js' setup_src='js/jscalendar/calendar-setup.js' lang='js/jscalendar/lang/calendar-en.js' css='js/jscalendar/calendar-system.css'}
<script type="text/javascript" src="js/design.js"></script>
<script type="text/javascript" src="js/phone.js"></script>
<script type="text/javascript" src="js/ajax/prototype.js"></script>
<script type="text/javascript" src="js/ajax/jquery-1.4.2.js"></script>
<script type="text/javascript" src="js/ajax/jquery-1.6.2.min.js"></script>
<script type="text/javascript">
    $p = jQuery.noConflict();
    var status = "{$unit_status}";
    {literal}
    $p(document).ready(function() {
        $p('#unit_status input:radio').change(function() {
            var selectedVal = $p("#unit_status input:radio:checked").val();
            if(4 == selectedVal){
                $p('#unitList').html(status);
            }
        });
        /*$p('#unit_status input:radio').click(function() {
            var selectedVal = $p("#unit_status input:radio:unchecked").val();
                $p('select').remove();
                $p('#unitList').hide(status);
        });*/
    });
    {/literal}
</script>
<script language="javascript">
$j = jQuery.noConflict();
var no_check=false;  var disclaimer=1; var picture=0; var template = 0;
{$js}
{literal}
$j(document).ready(function(){
	$j('#generate_delivery').click(function(){
		alert($j('#delivery_date').val());	
	});
});

function call_number (from_number, to_number){
    //alert(from_number+to_number);
    //return false;
    $j.ajax({
        url: 'http://192.168.1.52/systest.php',
        data: {from: from_number, to: to_number},
        dataType: "jsonp",
        success: function(data){
            //alert('ok');
        }
    });    
    return false;
};

 function checkForm()
 {
  if (no_check) return true;
  if (document.s1['name'].value=='') {alert('Order\'s name must be set.'); return false;}
  if (document.s1['address'].value=='') {alert('Order\'s address must be set.'); return false;}
  if (document.s1['town'].value=='') {alert('Order\'s town must be set.'); return false;}
  if (document.s1['zip'].value=='') {alert('Order\'s zip must be set.'); return false;}
  if (document.s1['tax_level'].type != 'hidden')
    if (document.s1['tax_level'].value=='') {alert('Taxation level must be set.'); return false;}
  if (document.s1['floor_nbr'].value=='') {alert('Floor must be set.'); return false;}
  if (typeof(document.s1['addition'])=='object')
    if (document.s1['addition'].value=='') {alert('Additional price must be set.'); return false;} 

  if (document.s1['cust_price'].value!=0)
  {
    if (document.s1['cust_description'].value=='')
       {
        alert('Custom price description must be set');
        return false;}
  }

   
  if (!IsNumeric(document.s1['zip'],'Zip must be numeric.')) return false;
  {/literal}
  {if $edit_phone_no}
  {literal}
  if (typeof(document.s1['phone1'])=='object' && document.s1['phone1'].value!='')
    if (!IsNumeric(document.s1['phone1'],'Phone must be numeric.')) return false;
  if (typeof(document.s1['phone2'])=='object' && document.s1['phone2'].value!='')
    if (!IsNumeric(document.s1['phone2'],'Phone must be numeric.')) return false;
  if (typeof(document.s1['phone3'])=='object' && document.s1['phone3'].value!='')
    if (!IsNumeric(document.s1['phone3'],'Phone must be numeric.')) return false;
  if (typeof(document.s1['phone4'])=='object' && document.s1['phone4'].value!='')
    if (!IsNumeric(document.s1['phone4'],'Extension must be numeric.')) return false;
  if (typeof(document.s1['w_phone1'])=='object' && document.s1['w_phone1'].value!='')
    if (!IsNumeric(document.s1['w_phone1'],'Work Phone must be numeric.')) return false;
  if (typeof(document.s1['w_phone2'])=='object' && document.s1['w_phone2'].value!='')
    if (!IsNumeric(document.s1['w_phone2'],'Work Phone must be numeric.')) return false;
  if (typeof(document.s1['w_phone3'])=='object' && document.s1['w_phone3'].value!='')
    if (!IsNumeric(document.s1['w_phone3'],'Work Phone must be numeric.')) return false;
  if (typeof(document.s1['w_phone4'])=='object' && document.s1['w_phone4'].value!='')
    if (!IsNumeric(document.s1['w_phone4'],'Extension must be numeric.')) return false;
  if (typeof(document.s1['fax1'])=='object' && document.s1['fax1'].value!='')
    if (!IsNumeric(document.s1['fax1'],'Fax must be numeric.')) return false;
  if (typeof(document.s1['fax2'])=='object' && document.s1['fax2'].value!='')
    if (!IsNumeric(document.s1['fax2'],'Fax must be numeric.')) return false;
  if (typeof(document.s1['fax3'])=='object' && document.s1['fax3'].value!='')
    if (!IsNumeric(document.s1['fax3'],'Fax must be numeric.')) return false;
  if (typeof(document.s1['fax4'])=='object' && document.s1['fax4'].value!='')
    if (!IsNumeric(document.s1['fax4'],'Extension must be numeric.')) return false;
  if (typeof(document.s1['cell1'])=='object' && document.s1['cell1'].value!='')
    if (!IsNumeric(document.s1['cell1'],'Cell phone must be numeric.')) return false;
  if (typeof(document.s1['cell2'])=='object' && document.s1['cell2'].value!='')
    if (!IsNumeric(document.s1['cell2'],'Cell phone must be numeric.')) return false;
  if (typeof(document.s1['cell3'])=='object' && document.s1['cell3'].value!='')
    if (!IsNumeric(document.s1['cell3'],'Cell phone must be numeric.')) return false;
  if (typeof(document.s1['cell4'])=='object' && document.s1['cell4'].value!='')
    if (!IsNumeric(document.s1['cell4'],'Extension must be numeric.')) return false;
  if (typeof(document.s1['floor_nbr'])=='object' && !IsNumeric(document.s1['floor_nbr'],'Floor must be numeric.')) return false;
  if (typeof(document.s1['addition'])=='object' && document.s1['addition'].value!='')
    if (!IsNumeric(document.s1['addition'],'Additional price must be numeric.')) return false;


  if((document.s1['phone1'].value!='' && document.s1['phone1'].value.length!=3) || document.s1['phone1'].value.length==3 && (document.s1['phone2'].value.length!=3 || document.s1['phone3'].value.length!=4) ||
     (document.s1['phone2'].value!='' && document.s1['phone2'].value.length!=3) || document.s1['phone2'].value.length==3 && (document.s1['phone1'].value.length!=3 || document.s1['phone3'].value.length!=4) ||
     (document.s1['phone3'].value!='' && document.s1['phone3'].value.length!=4) || document.s1['phone3'].value.length==4 && (document.s1['phone1'].value.length!=3 || document.s1['phone2'].value.length!=3))
     {alert('Wrong Phone format. Required format : XXX-XXX-XXXX'); return false;}
  if((document.s1['w_phone1'].value!='' && document.s1['w_phone1'].value.length!=3) || document.s1['w_phone1'].value.length==3 && (document.s1['w_phone2'].value.length!=3 || document.s1['w_phone3'].value.length!=4) ||
     (document.s1['w_phone2'].value!='' && document.s1['w_phone2'].value.length!=3) || document.s1['w_phone2'].value.length==3 && (document.s1['w_phone1'].value.length!=3 || document.s1['w_phone3'].value.length!=4) ||
     (document.s1['w_phone3'].value!='' && document.s1['w_phone3'].value.length!=4) || document.s1['w_phone3'].value.length==4 && (document.s1['w_phone1'].value.length!=3 || document.s1['w_phone2'].value.length!=3))
     {alert('Wrong Work Phone format. Required format : XXX-XXX-XXXX'); return false;}
  if((document.s1['cell1'].value!='' && document.s1['cell1'].value.length!=3) || document.s1['cell1'].value.length==3 && (document.s1['cell2'].value.length!=3 || document.s1['cell3'].value.length!=4) ||
     (document.s1['cell2'].value!='' && document.s1['cell2'].value.length!=3) || document.s1['cell2'].value.length==3 && (document.s1['cell1'].value.length!=3 || document.s1['cell3'].value.length!=4) ||
     (document.s1['cell3'].value!='' && document.s1['cell3'].value.length!=4) || document.s1['cell3'].value.length==4 && (document.s1['cell1'].value.length!=3 || document.s1['cell2'].value.length!=3))
     {alert('Wrong Cell format. Required format : XXX-XXX-XXXX'); return false;}
  if((document.s1['fax1'].value!='' && document.s1['fax1'].value.length!=3) || document.s1['fax1'].value.length==3 && (document.s1['fax2'].value.length!=3 || document.s1['fax3'].value.length!=4) ||
     (document.s1['fax2'].value!='' && document.s1['fax2'].value.length!=3) || document.s1['fax2'].value.length==3 && (document.s1['fax1'].value.length!=3 || document.s1['fax3'].value.length!=4) ||
     (document.s1['fax3'].value!='' && document.s1['fax3'].value.length!=4) || document.s1['fax3'].value.length==4 && (document.s1['fax1'].value.length!=3 || document.s1['fax2'].value.length!=3))
     {alert('Wrong Fax format. Required format : XXX-XXX-XXXX'); return false;}

  if(document.s1['phone1'].value=='' && document.s1['phone2'].value=='' && document.s1['phone3'].value=='' &&
     document.s1['w_phone1'].value=='' && document.s1['w_phone2'].value=='' && document.s1['w_phone3'].value=='' &&
     document.s1['fax1'].value=='' && document.s1['fax2'].value=='' && document.s1['fax3'].value=='' &&
     document.s1['cell1'].value=='' && document.s1['cell2'].value=='' && document.s1['cell3'].value=='')
     {alert('At least one of numbers ( phone, cell, fax ) must be set'); return false;}
     {/literal}
  {/if} 
  {if $data.pick_up eq 1}
     if (typeof(document.s1['_state'])=='object') document.s1['_state'].disabled=false;
     if (typeof(document.s1['address'])=='object') document.s1['address'].disabled = false; 
     if (typeof(document.s1['town'])=='object') document.s1['town'].disabled = false;
     if (typeof(document.s1['zip'])=='object') document.s1['zip'].disabled = false;
  {/if}
  {literal}
  
  for (var i=0; i < document.s1.status.length; i++)
   {
   if (document.s1.status[i].checked && document.s1.status[i].value == 11)
      {
	  document.s1['cancel_order_reason'].value = prompt("Enter the reason for canceling the order.","Enter information here.");
      }
   }

  return true;
 }
 function contractorChange()
 {
   if (document.s1['is_contructor'].value=='0') {
     document.s1['commition'].value='0';
     document.s1['commition'].disabled = true;
   } else document.s1['commition'].disabled = false;
 }
 function changeAInput(f,l,n)
 {
  if (document.s1[f].value.length >= l) document.s1[n].focus();
 }

 function decimalWrite(event,field)
 {
    if (event.srcElement) {kc =  event.keyCode;} else {kc =  event.which;}
    if ((kc < 48 || kc > 57) && kc != 8 && kc != 0)
     if (kc==46) {
       if (document.getElementById(field).value.indexOf('.')>=0) return false;
     } else return false;
    return true;
 }

 function decimalWriteM(event,field)
 {
    if (event.srcElement) {kc =  event.keyCode;} else {kc =  event.which;}
    if ((kc < 48 || kc > 57) && kc != 8 && kc != 0)
     if (kc==46) {
       if (document.getElementById(field).value.indexOf('.')>=0) return false;
     } else if (kc == 45)
       {
         if (document.getElementById(field).value.length>0) return false;
       } else return false;
    return true;
 }

 function clearFields()
 {
   document.s1['name'].value = '';  document.s1['address'].value = '';  document.s1['town'].value = '';
   document.s1['zip'].value = '';
   document.s1['phone1'].value = '';   document.s1['phone2'].value = ''; document.s1['phone3'].value = '';
   document.s1['fax1'].value = '';   document.s1['fax2'].value = ''; document.s1['fax3'].value = '';
   document.s1['cell1'].value = '';   document.s1['cell2'].value = ''; document.s1['cell3'].value = '';
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
 function changeTemplate(obj)
 {
   if (template==0) { template = 1; obj.alt = "Print only templates"; }
     else { template = 0; obj.alt = "Print all items"; }
   obj.src='gfx/pr_templates_'+template+'.gif';
 }


function wyrownaj()
{
document.s1['rownaj'].value=1;
document.s1.submit();
}

function changePickUp(obj)
 {
  document.s1['address'].disabled = false; document.s1['town'].disabled = false;
  document.s1['_state'].disabled = false;   document.s1['zip'].disabled = false;
  if (document.s1['pick_up'].checked)
  {
  document.s1['address'].value = Pick[5];
  document.s1['town'].value =Pick[6];
  document.s1['_state'].value =Pick[7];
  document.s1['zip'].value = Pick[8];
 /* document.s1['address'].disabled = true; document.s1['town'].disabled = true;
  document.s1['_state'].disabled = true;   document.s1['zip'].disabled = true;*/
  //no_check=true;  
  document.s1['pickup'].value=1; 
  //document.s1['no_template'].value=1;  jest uzyta wartosc pickupu
  document.s1.submit(); 
  }
  else 
  {
  document.s1['address'].value = Pick[1];
  document.s1['town'].value =Pick[2];
  document.s1['_state'].value =Pick[3];
  document.s1['zip'].value = Pick[4];
   no_check=true;  
   //document.s1['pickup'].value=0; 
   //document.s1['no_template'].value=0; 
   document.s1.submit(); 
  }
}


 function changePickUpstary(obj)
 {
  document.s1['address'].disabled = false; 
  document.s1['town'].disabled = false;
  document.s1['_state'].disabled = false; 
  document.s1['zip'].disabled = false;
  //document.s1['address'].removeAttribute('readonly'); document.s1['town'].readonly = false; document.s1['zip'].readonly = false;
  if (document.s1['pick_up'].checked)
  { no_check=true;  
   document.s1['pickup'].value=1; 
   document.s1['no_template'].value=1; 
   document.s1.submit(); 
  }
   else 
   {
    document.s1['no_template'].value=0; 
	document.s1['address'].value = '';
    document.s1['town'].value = '';
   }
 }

 function open_wnd(lnk)
 {
  open('search_ezip.php?'+lnk,'search_zip','top=100,left=200,width=500,height=300,toolbar=0,menu=0,scrollbars=1,resizable=1');
 }

 function open_window(lnk)
 {
  open(''+lnk,'Comparision','top=10,left=10,width=800,height=800,toolbar=0,menu=0,scrollbars=1,resizable=1');
 }


 function wyslij2()
 {

  if (checkForm())
 formord.submit();
  }
  
function put_slabs_onhold() {
	//alert(oid);
	var show = document.getElementById('slabs_on_hold').value;
	if (show == 1) {
		document.getElementById('slab_frame_search').style.display='inline'
	}
	else {
		document.getElementById('slab_frame_name').value=''
		document.getElementById('slab_frame_search').style.display='none'
		document.getElementById('onhold_result').innerHTML = ''
	}
}
function search_slab_onhold() {
	var frame = document.getElementById('slab_frame_name').value
	
	var url = "ord_slab_onhold.php";
	var pars = "?frame=" + frame;
	new Ajax.Updater('onhold_result', 'ord_slab_onhold.php'+pars, { method: 'get' });
}
function save_slab_onhold() {
	var frame = document.getElementById('slab_frame_name').value
	if (frame == "") { alert("No Slabs were found. Try again."); return false; }
	var oid = document.getElementById('id').value
	var qty = document.getElementById('slab_qty').value
	var dep = document.getElementById('slab_deposit').value
	var name = document.getElementById('slab_name')
	var name = name.options[name.selectedIndex].text
	var notes = document.getElementById('slab_notes').value
	
	var url = "ord_slab_onhold.php";
	var pars = "?frame=" + frame + "&oid=" + oid + "&qty=" + qty + "&dep=" + dep + "&name=" + name + "&notes=" + notes;
	new Ajax.Updater('onhold_result', 'ord_slab_onhold.php'+pars, { method: 'get', evalScripts: true });
}
function save_form_change(o) {
	var value = o.options[o.selectedIndex].value;
	{/literal}var oid = {if $id>0}{$id};{else}0;{/if}
	var aid = {$data.id_account};{literal}
	var pars = '?id='+oid+'&id_account='+aid+'&formval='+value;
	new Ajax.Request(
					'orderedit.php', 
					{
						method: 'get',
						parameters: pars,
						onComplete: function() {
										return;
									}
					});
}
function mark_pickup_done(id) {
	var pars = '?oid='+id+'&pickup=1';
	new Ajax.Request(
					'ajax_autoconfirm.php', 
					{
						method: 'post',
						parameters: pars,
						onComplete: function() {
										alert('Order has been marked as complete');
										return;
									}
					});
}
function confirmTask(type,id) {
	var url = "ajax_sch_confirm_task.php";
	var pars = "id="+id+"&type="+type;
	var xmlHttp = new Ajax.Request(
					url, 
					{
						method: 'get',
						parameters: pars,
						//onCreate: Loader(),
						//onLoading : Loader(),
						//onLoading: LoaderConfirm(id,type),
						//onInteractive: Loader(),
						onComplete: function() { 
							if(type == 0) {
								$j('#template_status').css("background-color", "#9FFF9F");
								$j('#template_status > td').html("<b>TEMPLATE CONFIRMED");
							}
							else {
								$j('#install_status').css("background-color", "#9FFF9F");
								$j('#install_status > td').html("<b>INSTALLATION CONFIRMED");
							}
						}
					});
}
{/literal}
</script>
{assign var="module_name" value="Work Order"}
{include file="module_header.tpl"}
<br>
{if $error<>''}<div class="text_black" align="center"><b><font color="red">{$error}</font></b><br></div>{/if}
{if $distance>100}<div class="text_black" align="center"><b><font color="gren" size="+2">
 Jobsite more than 100 miles away</font></b><br></div>{/if}
<center>
<table>
    <tr>
        <td valign="top">

{assign var="table_width" value="400"}
{if $id <> ''}
    {capture assign="table_headertitle"}Personal Data:{/capture}
{else}
    {assign var="table_headertitle" value="New Work Order"}
{/if}
<form id="formord" name="s1" method="POST" action="{$script}edit.php" onsubmit="return checkForm();">
{include file="table_header.tpl"}
<input type="hidden" name="cancel_order_reason" value="">
<input type="hidden" name="rownaj" value="0">
<input type="hidden" name="id" id="id" value="{$id}">
<input type="hidden" name="alastid" value="{$alastid}">
<input type="hidden" name="id_account" value="{$data.id_account}">
<input type="hidden" name="pickup" value="0">
<input type="hidden" name="is_contractor" value="{$data.is_contractor}">
<input type="hidden" name="commition" value="{$data.commition}">
<input type="hidden" name="discount" value="{$data.discount}">
<input type="hidden" name="tax_level" value="{$data.tax_level}">
<input type="hidden" name="old_zip" value="{$data.zip}">
<input type="hidden" name="zone_coef" value="{$data.zone_coef}">
<input type="hidden" name="zone_charge" value="{$data.zone_charge}">
<input type="hidden" name="old_town" value="{$data.town}">
<input type="hidden" name="no_template" value="0">
<input type="hidden" name="old_status" value="{$data.status}">
<input type="hidden" name="id_tax_district" value="{$data.id_tax_district}">
<input type="hidden" name="cr_date" value="{$data.cr_date}">
<input type="hidden" name="cr_name" value="{$data.cr_name}">
<input type="hidden" name="a_name" value="{$data.a_name}">
<input type="hidden" name="area" value="{$data.area}">
<input type="hidden" name="edge" value="{$data.edge}">
<input type="hidden" name="backsplash" value="{$data.backsplash}">
<input type="hidden" name="tot_price" value="{$data.tot_price}">
<input type="hidden" name="id_confirm" value="{$data.id_confirm}">
<input type="hidden" name="confirm_date" value="{$data.confirm_date}">
<input type="hidden" name="payment_level" value="{$data.payment_level}">
<input type="hidden" name="resale" value="{$data.resale}">
<input type="hidden" name="credit_app" value="{$data.credit_app}">
<input type="hidden" name="close_date" value="{$data.close_date}">
<input type="hidden" name="old_temp_date" value="{if $show_temp_date}{$data.temp_date}{/if}">
<input type="hidden" name="oldinv_number" value="{$data.inv_number}">
<input type="hidden" name="message" id="message" value="{$data.message}">
<input type="hidden" name="only_template" id="only_template" value="{$data.only_template}">
<input type="hidden" name="slabs_on_hold" id="slabs_on_hold" value="{$data.slabs_on_hold}">
<input type="hidden" name="oldmessage" id="message" value="{$data.message}">
<input type="hidden" name="old_cust_description" value="{$data.cust_description}">
<input type="hidden" name="old_cust_price" value="{$data.cust_price}">
<input type="hidden" name="old_addition" value="{$data.addition}">
<input type="hidden" name="ord_template_order" value="{$ord_template_order}">
<input type="hidden" name="old_name" value="{$data.name}">
{if $home_improv eq false}<input type="hidden" id="h_improvement" name="h_improvement" value="{if $data.h_improvement eq 1}1{else}0{/if}">{/if}

  {if $id<>''}
   <tr class="cell_reccolor_blue_01a">
     <td>ID:</td>
     <td nowrap>{$data.id} {if $can_always_print} {if $print_inv || $data.status != 10} {if $workerid == 80 || $workerid == 81 || $workerid == 1 || $print_inv_cr_date || $data.status != 10}&nbsp;<a href="#" onClick="window.open('print_order.php?oid={$id}&pict='+picture+'&disc='+disclaimer+'&ot='+template,'print_order','toolbar=1,menu=1,scrollbars=1,resizable=1');"><img src="gfx/printer.gif" border="0" alt="Print invoice.{if $can_always_print && !$can_print}Warning: no deposit on that order or invoice no. was not generated!{/if}"></a>
       <img src="gfx/pr_disc_1.gif" style="cursor:hand" border="0" alt="Print with disclaimers" onclick="changeDisclaimer(this)">&nbsp;
       <img src="gfx/pr_pict_0.gif" style="cursor:hand" border="0" alt="Print without drawings" onclick="changePicture(this)">
       <img src="gfx/pr_templates_0.gif" style="cursor:hand" border="0" alt="Print all items" onclick="changeTemplate(this)">{/if}{/if}
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       {if is_null($bal.value)} {assign var=$bal.value value=0} {/if}{/if}
       <img src="gfx/dollar.gif" onmouseover="this.src='gfx/dollar2.gif'" onmouseout="this.src='gfx/dollar.gif'" border="0" alt="Balance:  {math equation="y - x" x=$bal.value  y=$data.tot_price format="%.2f"} - Last payment:{$bal.name}" >
       
     </td>
   </tr>
   {/if}

   {if $inv_nbr eq true}
   {if $id<>''}                                                                                                                                                   
   <tr class="cell_reccolor_blue_01a">
     <td>Invoice*:</td>
     {if is_numeric($data.inv_number) && $data.inv_number>0}
     <td valign="midle">
        <table width="100%"><tr><td>
               <input type="text" name="inv_number" value="{$data.inv_number}" size="5" maxlength="20" onKeyPress="return writeNumber(event,'inv_number')">&nbsp;
        </td>
        {if $new_check_link}
        <td align="right">
               <input type="button" name="new_check" class="BUTTON_OK" value="New Check" onClick="location.href='checkedit.php?id_acc={$data.id_account}';">&nbsp;
        </td>
        {/if}
        </tr></table>
     </td>
     {else}
     <td>
        <table width="100%"><tr><td>
               <input type="submit" name="gen_inv" class="BUTTON_OK" value="Generate Invoice #">&nbsp;
               {if $sr==1}<img src="gfx/flag.gif" alt="All payments with cash" border="0">{/if}
        </td>
        {if $new_check_link}
        <td align="right">
               <input type="button" name="new_check" class="BUTTON_OK" value="New Check" onClick="location.href='checkedit.php?id_acc={$data.id_account}';">&nbsp;
        </td>
        {/if}
        </tr></table>
     </td>
     {/if}
   </tr>
   {/if}
   {/if}

   <tr class="cell_reccolor_blue_01b">
     <td>Name*: </td>
     <td><input type="text" name="name" value="{$data.name}" size="30" maxlength="50">&nbsp;&nbsp;
         {if $id==''}
         <a href="#" onClick="clearFields()"><img src="gfx/order_1.gif" onmouseover="this.src='gfx/order_0.gif'" onmouseout="this.src='gfx/order_1.gif'" border="1" alt="Different customer - clear fields"></a>
         <input type="image" src="gfx/loc1.gif" onmouseover="this.src='gfx/loc0.gif'" onmouseout="this.src='gfx/loc1.gif'" alt="Pickup - get parameters from location" onClick="no_check=true;">{/if}
     </td>
   </tr>
   {if $data.pick_up eq 1}{assign var=ronly value=' disabled'}{assign var=ronly2 value=' Readonly'}{else}{assign var=ronly value=''}{assign var=ronly2 value=''}{/if}
   <tr class="cell_reccolor_blue_01a">
     <td>Account:</td>
     <td><table border="0" cellspacing="0" cellpadding="0"><tr class="text_black"><td valign="middle">
        <a href="accountedit.php?id={$data.id_account}" >{if $data.is_contractor}  <font color="red" size="2">  <b>{$data.a_name}<br> (ID: {$data.id_account})</b></font> {else} {$data.a_name} (ID: <b>{$data.id_account}</b>){/if}  </a>{if $data.is_contractor}<b><font color="red"></font></b>{/if}
        {if $assign_acc}
            </td><td width="10"></td><td><a href="ordertoacc.php?oid={$data.id}&aid={$data.id_account}">
                <img src="gfx/dayoff1.gif" align="middle" onmouseover="this.src='gfx/dayoff0.gif'" onmouseout="this.src='gfx/dayoff1.gif'" border="0" alt="Assign different account to that order">
            </a>
        {/if}
        </td><td width="30"></td><td>{$flag}</td></tr></table>
     </td>
   </tr>
   <tr class="cell_reccolor_blue_01b">
     <td>Location:</td>
     <td>{$data.loc_name} (ID: <b>{$data.id_location}</b>)</td>
   </tr>
   <tr class="cell_reccolor_blue_01a">
     <td>Address*:</td>
     <td>{if $edit_phone_no}<input type="text" name="address" value="{$data.address}" size="30" maxlength="150"{$ronly}>
	{else}<input type="text" name="address" value="{$data.address}" size="30" maxlength="150" style="display: none;">{/if}
	</td> <!-- zmienilem ronly na ronly2 : LUK -->
   </tr>
   <tr class="cell_reccolor_blue_01b">
     <td>Town*:</td>
     <td>
        <input type="text" name="town" id="town" value="{$data.town}" size="30" maxlength="50"{$ronly}> <!-- zmienilem ronly na ronly2 : LUK Musialem poprawic na ronly(na readonly nie dziala js): Piotr-->
        <a href="#" onClick="javascript:open_wnd('town='+document.s1['town'].value)">
            <img src="gfx/show1.gif" align="middle" onmouseover="this.src='gfx/show0.gif'" onmouseout="this.src='gfx/show1.gif'" alt="Search a town" border="0">
        </a>
     </td>
   </tr>
   <tr class="cell_reccolor_blue_01a">
     <td>State*:</td>
     <td>
        <SELECT name="_state" size="1"{$ronly}>
        {html_options options=$state selected=$data._state}
        </SELECT>&nbsp;
        ZIP*:&nbsp;&nbsp;&nbsp;
         <input type="text" name="zip" id="zip" value="{$data.zip}" size="5" maxlength="5" onKeyPress="return writeNumber(event,'zip')" {$ronly}> <!-- zmienilem ronly na ronly2 : LUK -->
         <a href="#" onClick="javascript:open_wnd('zip='+document.s1['zip'].value)">
            <img src="gfx/show1.gif" align="middle" onmouseover="this.src='gfx/show0.gif'" onmouseout="this.src='gfx/show1.gif'" alt="Search zip code" border="0">
         </a>
     </td>
   </tr>
   <tr class="cell_reccolor_red_01b">
     <td>Phone:</td>
     <td>
         {if $id=='' || $edit_phone_no}
         <input type="text" name="phone1" value="{$data.phone1}" size="3" maxlength="3" onKeyPress="return writeNumber(event,'phone1')" onKeyUp="changeAInput('phone1',3,'phone2')"> -
         <input type="text" name="phone2" value="{$data.phone2}" size="3" maxlength="3" onKeyPress="return writeNumber(event,'phone2')" onKeyUp="changeAInput('phone2',3,'phone3')"> -
         <input type="text" name="phone3" value="{$data.phone3}" size="4" maxlength="4" onKeyPress="return writeNumber(event,'phone3')" onKeyUp="changeAInput('phone3',4,'phone4')">&nbsp;Ext:&nbsp;
         <input type="text" name="phone4" value="{$data.phone4}" size="4" maxlength="4" onKeyPress="return writeNumber(event,'phone4')">
         {else}hidden{/if} 
         {if $data.call_phone!=''}
            <a href="#" onClick="call_number('{$comp_name}', '{$data.phone1}{$data.phone2}{$data.phone3}');">
                <img src="gfx/calls_1.gif" align="middle" onmouseover="this.src='gfx/calls_0.gif'" onmouseout="this.src='gfx/calls_1.gif'" alt="Call '{$data.name}'" border="0">
            </a>
         {/if}
     </td>
   </tr>
   <tr class="cell_reccolor_red_01a">
     <td>Work Phone:</td>
     <td>
         {if $id=='' || $edit_phone_no}
         <input type="text" name="w_phone1" value="{$data.w_phone1}" size="3" maxlength="3" onKeyPress="return writeNumber(event,'w_phone1')" onKeyUp="changeAInput('w_phone1',3,'w_phone2')"> -
         <input type="text" name="w_phone2" value="{$data.w_phone2}" size="3" maxlength="3" onKeyPress="return writeNumber(event,'w_phone2')" onKeyUp="changeAInput('w_phone2',3,'w_phone3')"> -
         <input type="text" name="w_phone3" value="{$data.w_phone3}" size="4" maxlength="4" onKeyPress="return writeNumber(event,'w_phone3')" onKeyUp="changeAInput('w_phone3',4,'w_phone4')">&nbsp;Ext:&nbsp;
         <input type="text" name="w_phone4" value="{$data.w_phone4}" size="4" maxlength="4" onKeyPress="return writeNumber(event,'w_phone4')">
         {else}hidden{/if}
         {if $data.call_w_phone!=''}
            <a href="#" onClick="call_number('{$comp_name}', '{$data.w_phone1}{$data.w_phone2}{$data.w_phone3}');">
                <img src="gfx/calls_1.gif" align="middle" onmouseover="this.src='gfx/calls_0.gif'" onmouseout="this.src='gfx/calls_1.gif'" alt="Call '{$data.name}'" border="0">
            </a>
         {/if}
     </td>
   </tr>
   <tr class="cell_reccolor_red_01b">
     <td>Cell:</td>
     <td>
       {if $id=='' || $edit_phone_no}
       <input type="text" name="cell1" value="{$data.cell1}" size="3" maxlength="3" onKeyPress="return writeNumber(event,'cell1')" onKeyUp=changeAInput('cell1',3,'cell2')> -
       <input type="text" name="cell2" value="{$data.cell2}" size="3" maxlength="3" onKeyPress="return writeNumber(event,'cell2')" onKeyUp=changeAInput('cell2',3,'cell3')> -
       <input type="text" name="cell3" value="{$data.cell3}" size="4" maxlength="4" onKeyPress="return writeNumber(event,'cell3')" onKeyUp=changeAInput('cell3',4,'cell4')>&nbsp;Ext:&nbsp;
       <input type="text" name="cell4" value="{$data.cell4}" size="4" maxlength="4" onKeyPress="return writeNumber(event,'cell4')">
       {else}hidden{/if} 
       {if $data.call_cell!=''}
        <a href="#" onClick="call_number('{$comp_name}', '{$data.cell1}{$data.cell2}{$data.cell3}');">
            <img src="gfx/calls_1.gif" align="middle" onmouseover="this.src='gfx/calls_0.gif'" onmouseout="this.src='gfx/calls_1.gif'" alt="Call '{$data.name}'" border="0">
        </a>
       {/if}
     </td>
   </tr>
   <tr class="cell_reccolor_red_01a">
     <td>Fax:</td>
     <td nowrap>
         {if $id=='' || $edit_phone_no}
         <input type="text" name="fax1" value="{$data.fax1}" size="3" maxlength="3" onKeyPress="return writeNumber(event,'fax1')" onKeyUp=changeAInput('fax1',3,'fax2')> -
         <input type="text" name="fax2" value="{$data.fax2}" size="3" maxlength="3" onKeyPress="return writeNumber(event,'fax2')" onKeyUp=changeAInput('fax2',3,'fax3')> -
         <input type="text" name="fax3" value="{$data.fax3}" size="4" maxlength="4" onKeyPress="return writeNumber(event,'fax3')" onKeyUp=changeAInput('fax3',4,'fax4')>&nbsp;Ext:&nbsp;
         <input type="text" name="fax4" value="{$data.fax4}" size="4" maxlength="4" onKeyPress="return writeNumber(event,'fax4')">
         {else}hidden{/if} 
         
     </td>
   </tr>
   <tr class="cell_reccolor_green_01b">
     <td>Floor*:</td>
     <td><input type="text" name="floor_nbr" value="{$data.floor_nbr}" size="10" maxlength="10" onKeyPress="return decimalWrite(event,'floor_nbr')"></td>
   </tr>
   <tr class="cell_reccolor_green_01a">
     <td>Form:</td>
     <td>
       <SELECT name="form" size="1" onchange="save_form_change(this)">
         <option value="">----- No form released -----</option>
          {section name=frm loop=$forms}
            <option value="{$forms[frm]}"{if $forms[frm] == $data.form} selected{/if}>{$forms[frm]}</option>
          {/section}
       </SELECT>
     </td>
   </tr>
   <tr class="cell_reccolor_gold_01b">
     <td>Custom price:</td>
     <td><input type="text" name="cust_price" value="{$data.cust_price}" size="6" maxlength="8" onKeyPress="return decimalWriteM(event,'cust_price')">$
	     &nbsp;&nbsp;&nbsp;
		 {if $calculate}<input name="row" type="button" value="Calculate" class="BUTTON_OK" onclick="wyrownaj()"/>
	     {/if}
	 </td>
   </tr>
   <tr class="cell_reccolor_yellow_01a">
     <td valign="top">Custom price description:</td>
     <td>
       <textarea name="cust_description" rows="2" cols="30" maxlength="100">{$data.cust_description}</textarea>
     </td>
   </tr>
   {if $id!=''}
   {if is_numeric($data.yard_salesman)}
   <!-- Yard salesman is assigned-->
   <tr class="cell_reccolor_yellow_01a">
     <td valign="top">Yard salesman:</td><td>{$data.ysalesman}
     {if $ysalesman_clear}
            &nbsp;&nbsp;<input type="button" value="Clear" onClick="document.location.href='orderedit.php?id={$data.id}&id_account={$data.id_account}&assignme=1&clear=1'" class="BUTTON_CANCEL">
            <br><table width="100%" cellspacing="0" cellpadding="0"><tr><td height="3"><img width="0" height="0"></td></tr></table>
            <select id="y_salesman_list" style="z-index:100">
              {html_options values=$worker_list.value selected=$data.yard_salesman output=$worker_list.name}
            </select>
            <input type="button" value="Assign worker" onClick="document.location.href='orderedit.php?id={$data.id}&id_account={$data.id_account}&assign='+document.getElementById('y_salesman_list').value" class="BUTTON_OK">
     {/if}
     </td>
   </tr>
   <tr class="cell_reccolor_yellow_01b">
     <td>Yard Date:</td><td>{$data.yard_date|date_format:'%m/%d/%Y %H:%M'}</td>
   </tr>
   {elseif $crunits_cnt > 0}
   <!--Yard salesman is not assigned-->
   <tr class="cell_reccolor_yellow_01b">
     <td valign="top">Yard salesman:</td><td><input type="button" value="Assign me" onClick="document.location.href='orderedit.php?id={$data.id}&id_account={$data.id_account}&assignme=1'" class="BUTTON_OK">
     {if $ysalesman_clear}
            &nbsp;&nbsp;
            <br><table width="100%" cellspacing="0" cellpadding="0"><tr><td height="3"><img width="0" height="0"></td></tr></table>
               <select id="y_salesman_list">
              {html_options values=$worker_list.value selected=$data.yard_salesman output=$worker_list.name}
            </select>
            <input type="button" value="Assign worker" onClick="document.location.href='orderedit.php?id={$data.id}&id_account={$data.id_account}&assign='+document.getElementById('y_salesman_list').value" class="BUTTON_OK">
     {/if}
     </td>
   </tr>
   {/if}
 {/if}

 {if $data.state=="NY"}
    <tr class="cell_reccolor_yellow_01a">
     <td>Add Sales Tax (No Form):</td>
     <td><input type="checkbox" name="add_sales_tax" value="1"{if $data.add_sales_tax_ny == 1} checked{/if}></td>
   </tr>
   {/if}
   {if $home_improvement eq true}
   
   <tr class="cell_reccolor_yellow_01a">
     <td>Home improvement:</td>
     <td><input type="checkbox" name="h_improvement" value="1"{if $data.h_improvement eq 1} checked{/if} {if $home_improv eq false} disabled="disabled"{/if}></td>
   </tr>
   {else}
   <tr class="cell_reccolor_yellow_01a">
     <td></td>
     <td><input type="hidden" name="h_improvement" value="{if $data.h_improvement eq 1}1{else}0{/if}"></td>
   </tr>
   {/if}
   
   {*
  <!-- <tr class="cell_reccolor_yellow_01b">
     <td>No template:</td>
     <td>
        <input type="checkbox" name="no_template" value="1"{if $data.no_template eq 1} checked{/if}>
    </td>
   </tr>--> *}
   <tr class="cell_reccolor_yellow_01b">
     <td>Pick up:</td>
     <td>
        <input type="checkbox" name="pick_up" value="1"{if $data.pick_up eq 1} checked{/if} {if $wo_unit_lock==false}onClick="changePickUp()"{else} disabled="disabled"{/if}>
		{if $data.pick_up eq 1}<input name="gen_pickup" type="button" value="Generate Pickup Form" class="BUTTON_OK" onclick="javascript:open_window('generate_pickup_form.php?oid={$data.id}','pppp','top=10,left=10,width=400,height=400,toolbar=0,menu=0,scrollbars=0,resizable=0')" style="width:120px"/>
        <input type="button" name="completepickup" value="Mark Completed" class="button_ok" onclick='javascript:mark_pickup_done({$id})'/>
        {/if}
     </td>
   </tr>
   {if $data.pick_up eq 1}
   <tr class="cell_reccolor_yellow_01b">
     <td>Pick up Delivery:</td>
     <td>
        <input type="button" id="generate_delivery" value="Schedule Delivery" class="button_ok" style="width:100px"/> Date: <input type="text" id="delivery_date" value="{$today}" style="width: 70px" />
     </td>
   </tr>
   {/if}
   <tr class="cell_reccolor_yellow_01a">
     <td>Discount: {$discount}% </td>
     <td>
        <input type="checkbox" name="check_discount" value="1"{if $data.discount != 0} checked{/if} >
     </td>
   </tr>
      
   {if $can_unblock_wo && $id!=''}
   <tr class="cell_reccolor_yellow_01b">
     <td>Unblock WO:</td>
     <td>
        <input type="checkbox" name="unblocked" value="1"{if $data.unblocked eq 1} checked{/if}>
     </td>
   </tr>
   {/if}
   {if $message && $id!=''}
   <tr class="cell_reccolor_yellow_01a">
     <td>Message:</td>
     <td>
        <input type="checkbox" name="message_chck" id="message_chck" {if $data.message eq 1}checked {/if}onClick="document.getElementById('message').value= document.getElementById('message_chck').checked?1:0;">
     </td>
   </tr>
   {/if}
   {if $only_template && $id!=''}
   <tr class="cell_reccolor_yellow_01b">
     <td>Only template:</td>
     <td>
        <input type="checkbox" name="only_template_chck" id="only_template_chck" {if $data.only_template eq 1}checked {/if}onClick="document.getElementById('only_template').value= document.getElementById('only_template_chck').checked?1:0;">
     </td>
   </tr>
   {/if}
   {if ($show_slab_on_hold && $id!='') || is_array($slab_on_hold_data)}
   <tr class="cell_reccolor_yellow_01b">
     <td>Slabs on Hold: <input type="checkbox" name="slabs_on_hold_chck" id="slabs_on_hold_chck" onClick="document.getElementById('slabs_on_hold').value=document.getElementById('slabs_on_hold_chck').checked?1:0; put_slabs_onhold();"></td>
     <td>
		{if is_array($slab_on_hold_data)}
		{section name=xxx loop=$slab_on_hold_data}
		Stone: {$slab_on_hold_data[xxx].name} ({$slab_on_hold_data[xxx].frame}), Qty: {$slab_on_hold_data[xxx].number}<br />
		{/section}
		{/if}
		<div style="display:none" id="slab_frame_search"><input type="text" id="slab_frame_name" size="5" /> <input type="button" value="Search" onclick="search_slab_onhold();" /></div>
		<div id="onhold_result" style="display:inline"></div>
     </td>
   </tr>
   {/if}
   {if $id<>''}
   <tr class="cell_reccolor_gold_01a">
     <td>Cr. date:</td>
     <td>{$data.cr_date}</td>
   </tr>
   <tr class="cell_reccolor_gold_01b">
     <td>Cr. by:</td>
     <td>{$data.cr_name}</td>
   </tr>
   {/if}
   
{include file="table_footer.tpl"}
<center>
<br>
   <input type="button" name="cancel" class="BUTTON_CANCEL" value="Go Back" onClick="location.href='{$script}.php'">
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
   <input type="submit" name="submit_o" id="save_button" class="BUTTON_OK" value="Save"{if $editform == false or $wo_unit_lock==true} disabled{/if}>

</center>

</td>

{if $id=='' && ($data.is_contractor=='0' || $zunitu >0) && !$error && $delete_unit!=1}
<script language="javascript">
wyslij2();
</script>
{/if}

<td valign="top">


<table border="0" cellspacing="0" cellpadding="0">
<tr valign="top">
     <td>

    {if $id<>''}
        {if $stats!=''}
        {assign var="table_width" value="500"}
        {assign var="table_headertitle" value="Status<span id=\"status_act\"></span>:"}
        <form name="s1" method="POST" action="{$script}edit.php" onsubmit="return checkForm();">
        {include file="search_header.tpl"}
            <div id="ord_status">
                <td class="text_black">
                    {$stats}
                </td>
            </div>
        </tr>
        {include file="search_footer.tpl"}
        {/if}
        
        {*if $data.status==0 && $data.is_contractor == 0 && $data.deposit==0}
    
       
       {/if*}

         {if $show_temp_date}
        {assign var="table_headertitle" value="Assign template date:"}
        {assign var="table_width" value="500"}
        {include file="search_header.tpl"}

                 {if $templater_in_wo}
                 <tr class="cell_reccolor_neutral_01">
                   <td align="right">Templater:</td>
                   <td>
                          <input type="hidden" name="old_templater_in_wo_worker" value="{$templater_in_wo_worker}">
                          <select name="templater_in_wo_worker">
                              <option value="0">-- not assigned --</option>
                              {section name=templ loop=$templater_list}
                              <option value="{$templater_list[templ].id}"{if $templater_in_wo_worker==$templater_list[templ].id}{assign var="templater_cell" value=$templater_list[templ].cell}{assign var="templater_cell_n" value=$templater_list[templ].templater} selected{/if}>{$templater_list[templ].templater}</option>
                              {/section}
                          </select>
                   </td>
                 </tr>
                 {/if}
   <tr class="cell_reccolor_neutral_01">
     <td align="right">Template date:</td>
     <td><input type="text" name="temp_date" id="temp_date" value="{$data.temp_date}" size="10" maxlength="10">&nbsp;
            {dhtml_calendar inputField="temp_date" button="calendar_single" singleClick="true" align="T1" showOthers="true" ifFormat="%m/%d/%Y"}&nbsp;&nbsp;
			Time Request: <select name="temp_daytime_req" style="width: 70px">
            	<option value="0" {if $ord_template_daytime == 0} selected="selected"{/if}>Any Time</option>
                <option value="1" {if $ord_template_daytime == 1} selected="selected"{/if}>AM</option>
                <option value="2" {if $ord_template_daytime == 2} selected="selected"{/if}>MID</option>
                <option value="3" {if $ord_template_daytime == 3} selected="selected"{/if}>PM</option>
            </select>
            <input type="hidden" name="old_temp_daytime_req" value="{$ord_template_daytime}" />
            <!--<a href="#" onClick="altiX.MakeCall('81{$templater_cell}');"><img src="gfx/calls_1.gif" onmouseover="this.src='gfx/calls_0.gif'" onmouseout="this.src='gfx/calls_1.gif'" alt="{$templater_cell_n} - {$templater_cell}" border="0"></a>-->
     </td>
   </tr>
   {if $sch_notes != ''}
   <tr class="cell_reccolor_neutral_01">
   <td><font color="red"><b>Notes (today):</b></font></td>
   <td><font color="red"><b>{$sch_notes}</b></font></td>
   </tr>
   {/if}
   {if $sch_notes_tomorrow != ''}
   <tr class="cell_reccolor_neutral_01">
   <td><font color="red"><b>Notes (tomorrow):</b></font></td>
   <td><font color="red"><b>{$sch_notes_tomorrow}</b></font></td>
   </tr>
   {/if}
   {include file="search_footer.tpl"}

   {elseif trim($data.temp_date)!=''}
        {assign var="table_headertitle" value="Assign template date:"}
        {assign var="table_width" value="400"}
        {include file="search_header.tpl"}
   <tr class="cell_reccolor_yellow_01b">
     <td>Template date:</td><td>{$data.temp_date}</td>
   </tr>

       {include file="search_footer.tpl"}
       {/if}

       
       {assign var="table_headertitle" value="Estimate assign:"}
        {assign var="table_width" value="500"}
        {include file="search_header.tpl"}
       <tr>
        <td class="text_black">
           <select name="sel_est" size="1" style=" width: 390">
             <option value="0" {if $data.id_estimate eq 0}SELECTED{/if}>Not Assigned</option>
              {section name="est" loop=$estimates}
               <option value="{$estimates[est].id_est}" {if $data.id_estimate eq $estimates[est].id_est}SELECTED{/if}>[{$estimates[est].id_est}] {$estimates[est].namees} ------ {$estimates[est].name}</option>
              {/section}
           </select>
         </td>


       </tr>
       
       {if $data.id_estimate > 0 && $ysalesman_clear}
          <tr class="cell_reccolor_neutral_01">
             <td colspan="2">
                <a href="javascript:open_window('orderestimatecomp.php?oid={$data.id}')">Compare with Estimate</a>
             </td>
          </tr >
          {/if}
       
       {include file="search_footer.tpl"}
       
       
       
       </center>
  {*/if*}
{/if}



     </td>
      <td>&nbsp;
       

      </td>


     <td>




         {if $id<>''}
        {assign var="table_headertitle" value="Work Order Info:"}
        {assign var="table_width" value="400"}
        {assign var="table_link" value=""}
        {include file="table_header.tpl"}
      {*if $data.status>0*}
       {if $data.backsplash!='' && $data.area!=''}
       <tr class="cell_reccolor_lightblue_01a">
         <td>Surface area:</td>
         <td>{math equation="x - y" x=$data.area y=$data.backsplash format="%.2f"} sqft</td>
       </tr>
       {/if}
       {if $data.backsplash<>''}
       <tr class="cell_reccolor_lightblue_01b">
         <td>Backsplash area:</td>
         <td>{$data.backsplash} sqft</td>
       </tr>
       {/if}
       {if $data.edge<>''}
       <tr class="cell_reccolor_lightblue_01a">
         <td>Total edge length:</td>
         <td>{$data.edge} ft</td>
       </tr>
       {/if}
       {if $data.area<>''}
       <tr class="cell_reccolor_lightblue_01b">
         <td>Total area:</td>
         <td>{$data.area} sqft</td>
       </tr>
       {/if}
       {if $data.tot_price<>''}
	   {if $show_wo_price}
       <tr class="cell_reccolor_lightblue_01a">
         <td>Total price:</td>
         <td>${$data.tot_price|string_format:"%.2f"}</td>
       </tr>
	   {/if}
       {/if}

       {if $data.payment_level!=''}
       <tr class="cell_reccolor_lightblue_01a"><td>Status:</td><td>{$data.payment_level}</td></tr>
       {/if}
       {if $data.resale!=''}
       <tr class="cell_reccolor_lightblue_01a"><td>Resale:</td><td>{if $data.resale==1}Yes{else}No{/if}</td></tr>
       {/if}
       {if $data.credit_app!=''}
       <tr class="cell_reccolor_lightblue_01a"><td>Credit application:</td><td>{if $data.credit_app==1}Yes{else}No{/if}</td></tr>
       {/if}
       
       {if $addition_priv}
       <!--Additional custom price (hidden in material price)-->
       <tr class="cell_reccolor_lightblue_01b">
         <td>Additional price:</td>
         <td>
             $ <input type="text" name="addition" value="{$data.addition}" size="10" maxlength="10" onKeyPress="return decimalWrite(event,'addition')">
         </td>
       </tr>
       {/if}
       {if $id!='' && $extrahelp_priv}
       <!--Setting extra help-->
       <tr class="cell_reccolor_lightblue_01a">
         <td>Template Extra Help:</td>
         <td>
             <input type="text" name="temp_extrahelp" value="{$data.temp_extrahelp|string_format:'%d'}" size="10" maxlength="10" onKeyPress="return decimalWrite(event,'temp_extrahelp')">
         </td>
       </tr>
       <tr class="cell_reccolor_lightblue_01a">
         <td>Installation Extra Help:</td>
         <td>
             <input type="text" name="inst_extrahelp" value="{$data.inst_extrahelp|string_format:'%d'}" size="10" maxlength="10" onKeyPress="return decimalWrite(event,'inst_extrahelp')">
         </td>
       </tr>
       {else}
              <input type="hidden" name="temp_extrahelp" value="{$data.temp_extrahelp}">
              <input type="hidden" name="inst_extrahelp" value="{$data.inst_extrahelp}">
       {/if}
       {if $data.service_cnt!='' && $data.service_cnt>0}
       <tr class="cell_reccolor_lightblue_01b">
         <td>Number of services:</td>
         <td>{$data.service_cnt}</td>
       </tr>
       {/if}
<!--       </FORM> -->

       {if $data.id_confirm<>''}
       <tr class="cell_reccolor_lightblue_01a">
         <td>Confirmed by:</td>
         <td>{$data.id_confirm}</td>
       </tr>
       <tr class="cell_reccolor_lightblue_01b">
         <td>Confirmation date:</td>
         <td>{$data.confirm_date}</td>
       </tr>
       {/if}
       {if $data.close_date<>''}
       <tr class="cell_reccolor_lightblue_01a">
         <td>Closing date:</td>
         <td>{$data.close_date}</td>
       </tr>
       {/if}
       {if $crtemplate.temp_date != "" && strtotime($crtemplate.temp_date) >= strtotime($today)}
       <tr class="cell_reccolor_lightblue_01b" id="template_status" style="background-color: {if $crtemplate.message == 0}#FF9191{else}#9FFF9F{/if}">
         <td colspan="2" align="center"><b>TEMPLATE {if $crtemplate.message == 0}NOT CONFIRMED <input type="button" value="Confirm" class="button_ok" onclick="confirmTask(0,{$crtemplate.id})"/>{else}CONFIRMED{if $crtemplate.m_voicemail == 1} <b style="background-color:#FF9191">(VOICE MAIL)</b> <input type="button" value="Confirm" class="button_ok" onclick="confirmTask(0,{$crtemplate.id})"/>{/if}{/if}</b></td>
       </tr>
       {/if}
       {if $crinstall.inst_date != "" && strtotime($crinstall.inst_date) >= strtotime($today)}
       <tr class="cell_reccolor_lightblue_01b" id="install_status" style="background-color: {if $crinstall.message == 0}#FF9191{else}#9FFF9F{/if}">
         <td colspan="2" align="center"><b>INSTALLATION {if $crinstall.message == 0}NOT CONFIRMED <input type="button" value="Confirm" class="button_ok" onclick="confirmTask(1,{$crinstall.id})"/>{else}CONFIRMED{if $crinstall.m_voicemail == 1} <b style="background-color:#FF9191">(VOICE MAIL)</b> <input type="button" value="Confirm" class="button_ok" onclick="confirmTask(1,{$crinstall.id})"/>{/if}{/if}</b></td>
       </tr>
       {/if}
       {if $temp_schedule_info.temp_time != 0}
       <tr class="cell_reccolor_lightblue_01b">
         <td>Template Time:</td>
         <td style="font-size:12px"><strong>STOP# {$temp_schedule_info.order+1} | {$temp_schedule_info.temp_time} - {$temp_schedule_info.temp_time_to} | {$crtemplate.temp_date|date_format:'%m/%d/%Y'}</strong></td>
       </tr>
       {/if}
       {if $inst_schedule_info.inst_time != 0}
       <tr class="cell_reccolor_lightblue_01a">
         <td>Installation Time:</td>
         <td style="font-size:12px"><strong>STOP# {$inst_schedule_info.order+1} | {$inst_schedule_info.inst_time} - {$inst_schedule_info.inst_time_to} | {$crinstall.inst_date|date_format:'%m/%d/%Y'}</strong></td>
       </tr>
       {/if}
       {if $show_gps_wo_stops}
       <tr class="cell_reccolor_lightblue_01a">
         <td>Stops:</td>
         <td>
         {section name=gps loop=$gps_stops}
         	{if !$smarty.section.gps.first}{if $gps_stops[gps].stop_date != $old_date}{$gps_stops[gps].stop_date|date_format:'%m/%d/%Y'}<br />{/if}{else}{$gps_stops[gps].stop_date|date_format:'%m/%d/%Y'}<br />{/if}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$gps_stops[gps].stop_time}-{$gps_stops[gps].leave_time} | {$gps_stops[gps].stopped_time|truncate:5:"":true}<br />
            {assign var="old_date" value=$gps_stops[gps].stop_date}
         {/section}
         </td>
       </tr>
      {/if}
      {include file="table_footer.tpl"}
      <br />
{literal}
<script type="text/javascript">
function open_print() {
{/literal}
	window.open('pickup_cct_print.php?wos='+{$smarty.get.id},'print','scrollbars=yes,toolbar=yes,menu=no');
{literal}
}
</script>
{/literal}
  {if is_array($crunits) && count($crunits)>0}


        {capture assign="table_headertitle"}Units ({$crunits_cnt}): Print All Units: <a href="#" onclick="javascript:open_print();">(Production Print)</a> <a href="#" onClick="window.open('pickup_cct_print.php?wos={$smarty.get.id}&unitprice=1&pict=0&disc=0','print_order','toolbar=1,menu=1,scrollbars=1,resizable=1');">(Unit Break Down)</a>{/capture}
        {assign var="table_width" value="400"}
        {if $new_unit}{capture assign="table_link"}ord_unitedit.php?oid={$id}&id_account={$data.id_account}{/capture}{/if}
        {include file="table_header.tpl"}
        {assign var="table_link" value=""}
         <tr class="cell_reccolor_neutral_01">
           <td width="30">ID</td>
           <td width="160">Name</td>
           <td width="100">Application</td>
           <td width="60">Cr. date</td>
           <td width="50"></td>
         </tr>
         {section name=unt loop=$crunits}
         <tr class="{cycle values="cell_reccolor_gold_01a,cell_reccolor_gold_01b"}">
           <td valign="top">{$crunits[unt].id}</td>
           <td valign="top">{$crunits[unt].name}</td>
           <td valign="top">{$crunits[unt].app_name}</td>
           <td valign="top">{$crunits[unt].cr_date|date_format:'%m/%d/%Y'}</td>
           <td valign="top"><a href="ord_unitedit.php?uid={$crunits[unt].id}&oid={$id}&id_account={$data.id_account}"><img src="gfx/edit_one1.gif" onmouseover="this.src='gfx/edit_one0.gif'" onmouseout="this.src='gfx/edit_one1.gif'" alt="Edit unit '{$crunits[unt].id}' ({$crunits[unt].name})" border="0"></a>
          {if $unit_delete && $dp && $wo_unit_lock != true }
          <a href="orderedit.php?id={$id}&uid={$crunits[unt].id}&act=del&oid={$id}&id_account={$data.id_account}" onclick="return askme('order unit {$crunits[unt].id}')"><img src="gfx/trash1.gif" onmouseover="this.src='gfx/trash0.gif'" onmouseout="this.src='gfx/trash1.gif'" border="0" alt="Remove record '{$crunits[unt].id}'"></a>
		  {else}
		   
		  {/if}
           </td>
         </tr>
          
      






         {/section}
         {include file="table_footer.tpl"}
         <br />
        
  {/if}

   {if is_array($crestimates) && count($crestimates)>0}
  

        {capture assign="table_headertitle"}Estimates ({$crestimates|@count}):{/capture}
        {assign var="table_width" value="400"}
        {capture assign="table_link"}estimateedit.php?id_account={$data.id_account}{/capture}
        {include file="table_header.tpl"}
        {assign var="table_link" value=""}
         <tr class="cell_reccolor_neutral_01" align="center">
          <td width="30" class="table">ID</td>
          <td width="190" class="table">Name</td>
          <td width="150" class="table">Cr. date</td>
          <td width="30"></td>
        </tr>
       {section name=est loop=$crestimates}
       <tr class="{cycle values="cell_reccolor_orange_01a,cell_reccolor_orange_01b"}">
         <td valign="top">{$crestimates[est].id}</td>
         <td valign="top">{$crestimates[est].name}</td>
         <td valign="top">{$crestimates[est].cr_date|date_format:'%m/%d/%Y'}</td>
         <td><a href="estimateedit.php?id={$crestimates[est].id}&id_account={$data.id_account}"><img src="gfx/edit_one1.gif" onmouseover="this.src='gfx/edit_one0.gif'" onmouseout="this.src='gfx/edit_one1.gif'" alt="Edit estimate '{$crestimates[est].id}'" border="0"></a></td>
       </tr>
       {/section}
       {include file="table_footer.tpl"}
       <br />

   {else}
      <a class="text_black" href="estimateedit.php?id_account={$data.id_account}">Make new estimate</a><br>&nbsp;
   {/if}

     {if is_array($crorders) && count($crorders)>0}

        {capture assign="table_headertitle"}Other Work Orders ({$crorders|@count}):{/capture}
        {assign var="table_width" value="400"}
        {capture assign="table_link"}orderedit.php?id_account={$data.id_account}{/capture}
        {include file="table_header.tpl"}
        {assign var="table_link" value=""}
         <tr class="cell_reccolor_neutral_01" align="center">
           <td width="30">ID</td>
           <td width="190">Name</td>
           <td width="80">Cr. date</td>
           <td width="80">Status</td>
           <td width="20">&nbsp;</td>
         </tr>
         {section name=ord loop=$crorders}
         <tr class="{cycle values="cell_reccolor_green_01a,cell_reccolor_green_01b"}">
           <td valign="top">{$crorders[ord].id}</td>
           <td valign="top">{$crorders[ord].name}</td>
           <td valign="top">{$crorders[ord].cr_date|date_format:'%m/%d/%Y'}</td>
           <td valign="top">{$crorders[ord].status}</td>
           <td valign="top"><a href="orderedit.php?id={$crorders[ord].id}&id_account={$data.id_account}"><img src="gfx/edit_one1.gif" onmouseover="this.src='gfx/edit_one0.gif'" onmouseout="this.src='gfx/edit_one1.gif'" alt="Edit order '{$crorders[ord].id}'" border="0"></a></td>
         </tr>
         {/section}
        {include file="table_footer.tpl"}
 
  {/if}
{else}
 </FORM>
{/if}

     </td>
</tr></table>
{if $id<>''}

<table border="0" cellspacing="6" cellpadding="0">

<tr align="center">
     <td>{$log}</td>
</tr>
<tr>
     <td>{$log_ACC}</td>
</tr>
<tr>
     <td>{$log_EST}</td>
</tr>
<tr>
     <td>{$log_UNIT}</td>
</tr>
</table>

{/if}





</table>
</center>






<!--last column -->

<!-- <table width="90%"> -->

<br>

{include file="foot.tpl"}
