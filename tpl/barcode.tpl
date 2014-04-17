{include file="header_no_user.tpl"}
<script type="text/javascript" src="js/design.js"></script>
<script type="text/javascript" src="js/ajax/jquery-1.6.2.min.js"></script>
{literal}
<script type="text/javascript">
   function getfocus()
   {
        document.getElementById('workorder').focus()
   }
   $(document).ready(function(){
       jQuery.fx.off = true;
       $("#confirm").click(function(){
           $("#error").show();
       });

       $("#error").show(function(){
           $("#workorder").focus();
       });

       $("#confirm").click(function(){
           $("#msg").show();
       });

       $("#msg").show(function(){
           $("#workorder").focus();
       });

       $("#workorder").focusout(function(){
           var id = $(this).val();
           if(!isNaN(id) && id!='')
            loadData(id, 0);
       });
   });

   $(document).ready(function(){
       $("#employee").focusout(function(){
           var e = $(this).val();
           var arr;
           if(!isNaN(e))
               arr = new Array(e);
           else if(e.substring(0,2) == 'ID'){
               e = e.substring(3, e.length);
               arr = e.split(',');
           }
           var id = e.length;
           loadEmployee(0, arr);
       });
   });

   var xmlhttp;
   var spans= new Array("sp_1","sp_2","sp_3","sp_4","sp_5","sp_6");

   function loadData(uid, eid){
       $.ajax({
           type:"GET",
           url:"loadData.php",
           data:"uid=" + uid + "&eid=" +eid,
           dataType: "html",
           beforeSend:function(xhr){
               xhr.setRequestHeader("Ajax-Request", "true");
           },
           success:function(response){
               var c_status = response.substring(9,10);
               var p_status = response.substring(20,21);
               var i_status = response.substring(31,32);
               var len = response.length;
               var id_order = response.substring(42,len);

               $("#workorder").val("WO " + id_order + " UNIT " + uid);
               if(i_status=='1')
               {
                   document.getElementById('sp_6').className="type_aa_selected";
                   resetClass("sp_6");
               }else if(i_status=='0' && p_status=='2'){
                   document.getElementById('sp_5').className="type_aa_selected";
                   resetClass("sp_5");
               }else if(p_status=='1'){
                   document.getElementById('sp_4').className="type_aa_selected";
                   resetClass("sp_4");
               }else if(p_status=='0' && c_status=='2'){
                   document.getElementById('sp_3').className="type_aa_selected";
                   resetClass("sp_3");
               }else if(c_status=='1'){
                   document.getElementById('sp_2').className="type_aa_selected";
                   resetClass("sp_2");
               }else if(c_status=='0'){
                   document.getElementById('sp_1').className="type_aa_selected";
                   resetClass("sp_1");
               }

               if(id_order!=''){
                  if($("#employee").val()=='')
                        $('#msg').html("WORK ORDER UNIT ACCEPTED.&#13;&#10;PLEASE SCAN YOUR ID NUMBER BARCODE.");
                  else
                        $('#msg').html("WORK ORDER UNIT ACCEPTED.&#13;&#10;FOR YES PLEASE PRESS CONFIRM BUTTON.");
               }
           }
       });
   }

   function loadEmployee(uid, eid){
       var name = '';
       var re = '';
       var size = eid.length;

       var node = document.getElementById('ep');
       while (node.hasChildNodes()) {
           node.removeChild(node.lastChild);
       }


       for(var i=0; i<size; i++)
       {

           (function(e){$.ajax({
               type:"GET",
               url:"loadData.php",
               data:"uid=" + uid + "&eid=" +eid[e],
               dataType: "html",
               beforeSend:function(xhr){
                   xhr.setRequestHeader("Ajax-Request", "true");
               },
               success:function(response){
                   name = response+' ';
                   name = "<div style='display: inline'>"+name+"</div>";
                   $('#ep').append(name);

                   var wo = $("#workorder").val();
                   if(wo=='')
                        $('#msg').html("EMPLOYEE ID ACCEPTED.&#13;&#10;PLEASE SCAN THE WORK ORDER UNIT BARCODE.");
                   else
                        $('#msg').html("EMPLOYEE ID ACCEPTED.&#13;&#10;FOR YES PLEASE PRESS CONFIRM BUTTON.");
               }
           });
       })(i)
       }


       $("#employee").val("ID " + eid);
   }

   function loadStatus(uid, eid){
       $.ajax({
           type:"GET",
           url:"loadData.php",
           data:"uid=" + uid + "&eid=" +eid,
           dataType: "html",
           beforeSend:function(xhr){
               xhr.setRequestHeader("Ajax-Request", "true");
           },
           success:function(response){
               var c_status = response.substring(9,10);
               var p_status = response.substring(20,21);
               var i_status = response.substring(31,32);
               var len = response.length;
               var id_order = response.substring(42,len);

               if(i_status=='1')
               {
                   document.getElementById('sp_6').className="type_aa_selected";
                   resetClass("sp_6");
               }else if(i_status=='0' && p_status=='2'){
                   document.getElementById('sp_5').className="type_aa_selected";
                   resetClass("sp_5");
               }else if(p_status=='1'){
                   document.getElementById('sp_4').className="type_aa_selected";
                   resetClass("sp_4");
               }else if(p_status=='0' && c_status=='2'){
                   document.getElementById('sp_3').className="type_aa_selected";
                   resetClass("sp_3");
               }else if(c_status=='1'){
                   document.getElementById('sp_2').className="type_aa_selected";
                   resetClass("sp_2");
               }else if(c_status=='0'){
                   document.getElementById('sp_1').className="type_aa_selected";
                   resetClass("sp_1");
               }
           }
       });
   }

   function loadStatus2(uid, eid){
       var name = '';
       var re = '';
       var arr;
       if(!isNaN(eid))
           arr = new Array(eid+'');
       else if(eid.substring(0,2) == 'ID'){
           eid = eid.substring(3, e.length);
           arr = eid.split(',');
       }

       var size = arr.length;
       var node = document.getElementById('ep');
       while (node.hasChildNodes()) {
           node.removeChild(node.lastChild);
       }


       for(var i=0; i<size; i++)
       {
           (function(e){$.ajax({
               type:"GET",
               url:"loadData.php",
               data:"uid=" + uid + "&eid=" +arr[e],
               dataType: "html",
               beforeSend:function(xhr){
                   xhr.setRequestHeader("Ajax-Request", "true");
               },
               success:function(response){
                   name = response+' ';
                   name = "<div style='display: inline'>"+name+"</div>";
                   $('#ep').append(name);
               }
           });
           })(i)
       }
   }

   function resetClass(id){
       for(var i=0; i<6; i++){
           if(spans[i]!=id){
               document.getElementById(spans[i]).className = "";
           }
       }
   }

   function selectPhase(){
       var el = document.getElementById("overlay");
       el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
   }

   function close() {
       document.getElementById("overlay").style.visibility = 'hidden';
   }

   function select(){
       var e = document.getElementById('stageDropDown');
       var st = e.options[e.selectedIndex].value;
       document.getElementById('stage').value = st;
       document.getElementById("overlay").style.visibility = 'hidden';
   }

</script>
<style>
.input {
  margin: 0px 0 0px 15px;
  background: white;
  float: left;
  clear: both;
}
.input span {
  position: absolute;
  padding: 0px;
  margin-left: 3px;
  color: #999;
}
.input input, .input textarea, .input select {
  position: relative;
  margin: 0;
  border-width: 1px;
  padding: 0px;
  background: transparent;
  font: inherit;
}

.type_aa span
{
    font-size: 16px;
    margin: 4px;
    color: #7EB6FF;
    padding: 3px;
    font-weight: normal;
}

span.type_aa_selected
{
    background-color: #EEFE84;
    border: 1px  #000000 solid;
}

.td{
    text-align: right;
 }
.close { text-decoration: underline }
#overlay {
    visibility: hidden;
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100px;
    text-align:center;
    z-index: 200;
    opacity:2.0;
}
#overlay div {
    width:250px;
    margin: 200px auto;
    background-color: #ffffff;
    border:2px solid #000;
    text-align:center;
}
</style>
{/literal}

{assign var="module_name" value="Work Order Check In"}
{include file="module_header.tpl"}

{assign var="table_width" value="1022"}
{assign var="table_headertitle" value="Scan Screen"}
{include file="table_header.tpl"}

<!---
<tr>
    <td colspan="2" align="center">
         <div id="error">{if $error<>''} <br><b><font color="red" size="40">{$error}</font></b><br>{/if}</div>
        {if $msg<>''}<br><div id="msg" style="display: none;"><b><font color="blue" size="40">{$msg}</font></b></div><br>{/if}
        {if $alert<>''}

        <script type="text/javascript">
            var alert = "{$alert}";
            var uid = "{$uid}";
            var eid = "{$eid}";
            {literal}
            if (confirm(alert)) {
                document.location.href="barcode.php?id_unit=" +uid+"&id_worker="+eid;
            } else {
                    $("#error").html("<br><b><font color='red' size='40'>Scan your Work Order Unit Barcode</font></b><br>");
            }
            {/literal}
        </script>

        {/if}
    </td>
</tr>
---->
<tr ><td>
    <form action="barcode.php" method="post" name="barcode" id="barcode-form" >
    <div style="float: right">
        <label class="input">
        <textarea name="msg" id="msg" cols="100" rows="2" class="input" disabled="true"
               style="height: 108px; width: 987px; text-align: left; border: solid 1px #000000;
               padding-left: 10;padding-top: 10;
            font-size: 34px; margin: 15px 15px 15px 0; color: #ffffff; resize: none; overflow: hidden;
            font-weight: 500; background-color: #7EB6FF" >{$error}&#x00A;
        </textarea >
    </div>
    </td>
</tr>
<tr><td>
    <div style="float: left">
    <label class="input">
        <input type="text" name="workorder" id="workorder" value="" style="height: 115px; padding-left: 10px;width: 350px; text-align: left; font-size: 50px; color: #800000; font-weight: bold;">
    </label>
    <label class="input">
        <input type="hidden" name="stage" id="stage" value="0">
    </label>
    </div>
    <div style="float: right">
        <input type="SUBMIT" value="CANCEL" name="cancel" id="cancel" class="BUTTON_OK"
               style="height: 115px; width: 300px; text-align: center;
               font-size: 55px; margin: 0px 15px 15px 0; color: #ffffff;
               font-weight: bold; background-color: #90ee90">
    </div>
    <div style="float: right">
        <input type="SUBMIT" value="CONFIRM" name="confirm" id="confirm" class="BUTTON_OK"
               style="height: 115px; width: 300px; text-align: center;
               font-size: 55px; margin: 0px 15px 15px 0; color: #ffffff;
               font-weight: bold; background-color: #EEB4B4" >
    </div>
    </form>
</td></tr>
{include file="table_footer.tpl"}

<table width="1022px" border="0" align="center" cellpadding="0" cellspacing="0">

    <tr>
        <td width="333" height="1"><img src="gfx/spacer.gif" width="1" height="1"></td>
    </tr>
    <tr>
        <td><table width="{math equation="x-y" x=$table_width y=2}" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="cell_table_lefttophard_01"><img src="gfx/spacer.gif" width="3" height="3"></td>
                    <td width="333" class="cell_table_top"><img src="gfx/spacer.gif" width="333" height="3"></td>
                    <td class="cell_table_righttophard_01"><img src="gfx/spacer.gif" width="3" height="3"></td>
                </tr>
                <tr>
                    <td class="cell_table_left"><img src="gfx/spacer.gif" width="3" height="3"></td>
                    <td width="{math equation="x-y" x=$table_width y=2}" class="cell_tablecenter">
                        <table width="{math equation="x-y" x=$table_width y=2}" border="0" cellpadding="0" cellspacing="15">
                            <tr>
                                <td colspan="2" width="987px" style="border: solid 1px #000000;">
                                    <table width="987px">
                                        <tr>
                                            <td style="float: left">
                                                <div style="float: left; padding-left: 10px; color: #7EB6FF;font-size: 20px;font-weight: bold">
                                                    EMPLOYEE:
                                                </div>
                                            </td>
                                            <td style="float: right">
                                                <div style="float: left; padding-left: 10px; color: #7EB6FF;font-size: 20px;font-weight: bold">
                                                    <div id="ep" style="float:right; display: inline; text-align: right">afhakfjka</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table></td>
                    <td class="cell_table_right"><img src="gfx/spacer.gif" width="3" height="3"></td>
                </tr>
                <tr>
                    <td class="cell_table_ld"><img src="gfx/spacer.gif" width="1" height="1"></td>
                    <td width="333" class="cell_table_down"><img src="gfx/spacer.gif" width="333" height="3"></td>
                    <td class="cell_table_rd"><img src="gfx/spacer.gif" width="1" height="1"></td>
                </tr>
            </table></td>
    </tr>
</table>

<table width="1022px" border="0" align="center" cellpadding="0" cellspacing="0">

    <tr>
        <td width="333" height="1"><img src="gfx/spacer.gif" width="1" height="1"></td>
    </tr>
    <tr>
        <td><table width="{math equation="x-y" x=$table_width y=2}" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="cell_table_lefttophard_01"><img src="gfx/spacer.gif" width="3" height="3"></td>
                    <td width="333" class="cell_table_top"><img src="gfx/spacer.gif" width="333" height="3"></td>
                    <td class="cell_table_righttophard_01"><img src="gfx/spacer.gif" width="3" height="3"></td>
                </tr>
                <tr>
                    <td class="cell_table_left"><img src="gfx/spacer.gif" width="3" height="3"></td>
                    <td width="{math equation="x-y" x=$table_width y=2}" class="cell_tablecenter">
                        <table width="{math equation="x-y" x=$table_width y=2}" border="0" cellpadding="0" cellspacing="15">
                            <tr>
                                <td colspan="2" width="987px" style="border: solid 1px #000000;">
                                    <table width="987px">
                                        <tr>
                                            <td style="float: left">
                                                <div style="float: left; padding-left: 10px; color: #7EB6FF;font-size: 20px;font-weight: bold">
                                                    ASSIGNED WORK ORDER/SERVICE:
                                                </div>
                                            </td>
                                            <td style="float: right">
                                                <div style="float: left; padding-left: 10px; color: #7EB6FF;font-size: 20px;font-weight: bold">
                                                    <div id="ep" style="float:right; display: inline; text-align: right">afhakfjka</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table></td>
                    <td class="cell_table_right"><img src="gfx/spacer.gif" width="3" height="3"></td>
                </tr>
                <tr>
                    <td class="cell_table_ld"><img src="gfx/spacer.gif" width="1" height="1"></td>
                    <td width="333" class="cell_table_down"><img src="gfx/spacer.gif" width="333" height="3"></td>
                    <td class="cell_table_rd"><img src="gfx/spacer.gif" width="1" height="1"></td>
                </tr>
            </table></td>
    </tr>
</table>

<table width="1022px" border="0" align="center" cellpadding="0" cellspacing="0">

    <tr>
        <td width="333" height="1"><img src="gfx/spacer.gif" width="1" height="1"></td>
    </tr>
    <tr>
        <td><table width="{math equation="x-y" x=$table_width y=2}" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="cell_table_lefttophard_01"><img src="gfx/spacer.gif" width="3" height="3"></td>
                    <td width="333" class="cell_table_top"><img src="gfx/spacer.gif" width="333" height="3"></td>
                    <td class="cell_table_righttophard_01"><img src="gfx/spacer.gif" width="3" height="3"></td>
                </tr>
                <tr>
                    <td class="cell_table_left"><img src="gfx/spacer.gif" width="3" height="3"></td>
                    <td width="{math equation="x-y" x=$table_width y=2}" class="cell_tablecenter">
                        <table width="{math equation="x-y" x=$table_width y=2}" border="0" cellpadding="0" cellspacing="15">
                            <tr>
                                <td colspan="2" width="987px" style="border: solid 1px #000000;">
                                    <table width="987px">
                                        <tr>
                                            <td style="float: left">
                                                <div style="float: left; padding-left: 10px; color: #7EB6FF;font-size: 20px;font-weight: bold">
                                                    ASSIGNED UNIT/UNITS:
                                                </div>
                                            </td>
                                            <td style="float: right">
                                                <div style="float: left; padding-left: 10px; color: #7EB6FF;font-size: 20px;font-weight: bold">
                                                    <div id="ep" style="float:right; display: inline; text-align: right">afhakfjka</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table></td>
                    <td class="cell_table_right"><img src="gfx/spacer.gif" width="3" height="3"></td>
                </tr>
                <tr>
                    <td class="cell_table_ld"><img src="gfx/spacer.gif" width="1" height="1"></td>
                    <td width="333" class="cell_table_down"><img src="gfx/spacer.gif" width="333" height="3"></td>
                    <td class="cell_table_rd"><img src="gfx/spacer.gif" width="1" height="1"></td>
                </tr>
            </table></td>
    </tr>
</table>

<table width="1022px" border="0" align="center" cellpadding="0" cellspacing="0">

    <tr>
        <td width="333" height="1"><img src="gfx/spacer.gif" width="1" height="1"></td>
    </tr>
    <tr>
        <td><table width="{math equation="x-y" x=$table_width y=2}" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="cell_table_lefttophard_01"><img src="gfx/spacer.gif" width="3" height="3"></td>
                    <td width="333" class="cell_table_top"><img src="gfx/spacer.gif" width="333" height="3"></td>
                    <td class="cell_table_righttophard_01"><img src="gfx/spacer.gif" width="3" height="3"></td>
                </tr>
                <tr>
                    <td class="cell_table_left"><img src="gfx/spacer.gif" width="3" height="3"></td>
                    <td width="{math equation="x-y" x=$table_width y=2}" class="cell_tablecenter">
                        <table width="{math equation="x-y" x=$table_width y=2}" border="0" cellpadding="0" cellspacing="15">
                            <tr>
                                <td colspan="2" width="987px" style="border: solid 1px #000000;">
                                    <table width="987px">
                                        <tr>
                                            <td style="float: left">
                                                <div style="float: left; padding-left: 10px; color: #7EB6FF;font-size: 20px;font-weight: bold">
                                                    ASSIGNED OTHER EMPLOYEES:
                                                </div>
                                            </td>
                                            <td style="float: right">
                                                <div style="float: left; padding-left: 10px; color: #7EB6FF;font-size: 20px;font-weight: bold">
                                                    <div id="ep" style="float:right; display: inline; text-align: right">afhakfjka</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table></td>
                    <td class="cell_table_right"><img src="gfx/spacer.gif" width="3" height="3"></td>
                </tr>
                <tr>
                    <td class="cell_table_ld"><img src="gfx/spacer.gif" width="1" height="1"></td>
                    <td width="333" class="cell_table_down"><img src="gfx/spacer.gif" width="333" height="3"></td>
                    <td class="cell_table_rd"><img src="gfx/spacer.gif" width="1" height="1"></td>
                </tr>
            </table></td>
    </tr>
</table>

<table width="1022px" border="0" align="center" cellpadding="0" cellspacing="0">

    <tr>
        <td width="333" height="1"><img src="gfx/spacer.gif" width="1" height="1"></td>
    </tr>
    <tr>
        <td><table width="{math equation="x-y" x=$table_width y=2}" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="cell_table_lefttophard_01"><img src="gfx/spacer.gif" width="3" height="3"></td>
                    <td width="333" class="cell_table_top"><img src="gfx/spacer.gif" width="333" height="3"></td>
                    <td class="cell_table_righttophard_01"><img src="gfx/spacer.gif" width="3" height="3"></td>
                </tr>
                <tr>
                    <td class="cell_table_left"><img src="gfx/spacer.gif" width="3" height="3"></td>
                    <td width="{math equation="x-y" x=$table_width y=2}" class="cell_tablecenter">
                        <table width="{math equation="x-y" x=$table_width y=2}" border="0" cellpadding="0" cellspacing="15">
                            <tr>
                                <td colspan="2" width="987px" style="border: solid 1px #000000;">
                                    <table width="987px">
                                        <tr>
                                            <td style="float: left">
                                                <div style="float: left; padding-left: 10px; color: #7EB6FF;font-size: 20px;font-weight: bold">
                                                    ASSIGNED EQUIPMENT:
                                                </div>
                                            </td>
                                            <td style="float: right">
                                                <div style="float: left; padding-left: 10px; color: #7EB6FF;font-size: 20px;font-weight: bold">
                                                    <div id="ep" style="float:right; display: inline; text-align: right">afhakfjka</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table></td>
                    <td class="cell_table_right"><img src="gfx/spacer.gif" width="3" height="3"></td>
                </tr>
                <tr>
                    <td class="cell_table_ld"><img src="gfx/spacer.gif" width="1" height="1"></td>
                    <td width="333" class="cell_table_down"><img src="gfx/spacer.gif" width="333" height="3"></td>
                    <td class="cell_table_rd"><img src="gfx/spacer.gif" width="1" height="1"></td>
                </tr>
            </table></td>
    </tr>
</table>

<table width="1022px" border="0" align="center" cellpadding="0" cellspacing="0">

    <tr>
        <td width="333" height="1"><img src="gfx/spacer.gif" width="1" height="1"></td>
    </tr>
    <tr>
        <td><table width="{math equation="x-y" x=$table_width y=2}" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="cell_table_lefttophard_01"><img src="gfx/spacer.gif" width="3" height="3"></td>
                    <td width="333" class="cell_table_top"><img src="gfx/spacer.gif" width="333" height="3"></td>
                    <td class="cell_table_righttophard_01"><img src="gfx/spacer.gif" width="3" height="3"></td>
                </tr>
                <tr>
                    <td class="cell_table_left"><img src="gfx/spacer.gif" width="3" height="3"></td>
                    <td width="{math equation="x-y" x=$table_width y=2}" class="cell_tablecenter">
                        <table width="{math equation="x-y" x=$table_width y=2}" border="0" cellpadding="0" cellspacing="15">
                            <tr>
                                <td colspan="2" width="987px" style="border: solid 1px #000000;">
                                    <table width="987px">
                                        <tr>
                                            <td colspan="2" class="type_aa">
                                                <div id="sp" style="padding-bottom: 8px; padding-left: 5px">
                                                    {if $uid!=''}<script>loadStatus({$uid}, 0);</script>{/if}<span id="sp_1">AWAITING CUTTING</span>
                                                    <span id="sp_2">CUTTING</span>
                                                    <span id="sp_3">AWAITING FABRICATION/CNC</span>
                                                    <span id="sp_4">FABRICATION/CNC</span>
                                                    <span id="sp_5">AWAITING INSTALLATION</span>
                                                    <span id="sp_6">INSTALLATION</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="margin-right: 10px">
                                                <div style="float: left; color: #7EB6FF;font-size: 16px;">
                                                    <input type="button" value="MOVE PHASE BACKWARDS" name="select_phase" id="phase"
                                                           style="width: 482px; height: 40px; clear: left; background-color: #82ccbe; color: #ffffff;
                                                           margin-right: 0px;font-size: 30px; font-weight: bold" onclick="selectPhase()">
                                                </div>
                                            </td>
                                            <td>
                                                <div style="float: right; color: #7EB6FF;font-size: 16px;">
                                                    <input type="button" value="MOVE PHASE FORWARD" name="select_phase" id="phase"
                                                           style="width: 482px; height: 40px; clear: left; background-color: #967dcc; color: #ffffff; font-size: 30px; font-weight: bold" onclick="selectPhase()">
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table></td>
                    <td class="cell_table_right"><img src="gfx/spacer.gif" width="3" height="3"></td>
                </tr>
                <tr>
                    <td class="cell_table_ld"><img src="gfx/spacer.gif" width="1" height="1"></td>
                    <td width="333" class="cell_table_down"><img src="gfx/spacer.gif" width="333" height="3"></td>
                    <td class="cell_table_rd"><img src="gfx/spacer.gif" width="1" height="1"></td>
                </tr>
            </table></td>
    </tr>
</table>

</td></tr>

<div id="overlay">
    <div>
        <br>
        <p>Please select the phase you want to start.</p>
        <select id="stageDropDown">
            <option label="op1" value="1">Cutting</option>
            <option label="op2" value="2">Fabrication/CNC</option>
            <option label="op3" value="3">Installation</option>
        </select>
        <br><br>
        <span style="text-align: left; padding-right: 40px;"><a href="javascript:select()">Select</a></span>
        <span style="text-align: left; padding-right: 20px;"><a href="javascript:close()">Close</a></span>
        <br><br>
    </div>
</div>

{literal}
<script>
(function($) {
    function toggleLabel() {
        var input = $(this);
        setTimeout(function() {
            var def = input.attr('title');
            if (!input.val() || (input.val() == def)) {
                input.prev('span').css('visibility', '');
                if (def) {
                    var dummy = $('<label></label>').text(def).css('visibility','hidden').appendTo('body');
                    input.prev('span').css('margin-left', dummy.width() + 3 + 'px');
                    dummy.remove();
                }
            } else {
                input.prev('span').css('visibility', 'hidden');
            }
        }, 0);
    };

    function resetField() {
        var def = $(this).attr('title');
        if (!$(this).val() || ($(this).val() == def)) {
            $(this).val(def);
            $(this).prev('span').css('visibility', '');
        }
    };

    $('input, textarea').live('keydown', toggleLabel);
    $('input, textarea').live('paste', toggleLabel);
    $('select').live('change', toggleLabel);

    $('input, textarea').live('focusin', function() {
        $(this).prev('span').css('color', '#ccc');
    });
    $('input, textarea').live('focusout', function() {
        $(this).prev('span').css('color', '#999');
    });

    $(function() {
        $('input, textarea').each(function() { toggleLabel.call(this); });
    });

})(jQuery);

</script>

{/literal}
</body>
</html>