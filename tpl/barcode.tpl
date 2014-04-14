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
           loadEmployee(0, e);
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
                  $('#msg').html("WORK ORDER UNIT ACCEPTED.&#13;&#10;PLEASE SCAN YOUR ID NUMBER BARCODE.");
               }
           }
       });
   }

   function loadEmployee(uid, eid){
       $.ajax({
           type:"GET",
           url:"loadData.php",
           data:"uid=" + uid + "&eid=" +eid,
           dataType: "html",
           beforeSend:function(xhr){
               xhr.setRequestHeader("Ajax-Request", "true");
           },
           success:function(response){
               $("#employee").attr('align', 'left');
               $("#employee").val("ID " + eid);

               document.getElementById('ep').innerHTML = response;
               if(response!=''){
                   $('#msg').html("EMPLOYEE ID ACCEPTED.&#13;&#10;FOR YES PRESS CONFIRM .");
               }
           }
       });
   }

   function resetClass(id){
       for(var i=0; i<6; i++){
           if(spans[i]!=id){
               document.getElementById(spans[i]).className = "";
           }
       }
   }

</script>
<style>
.input {
  margin: 15px 0 0px 15px;
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

</style>
{/literal}

{assign var="module_name" value="Work Order Check In"}
{include file="module_header.tpl"}
<br>

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
    <form action="barcode.php" method="post" name="barcode" id="barcode-form">
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
        <span style="height: 51px; width: 375px; text-align: left; font-size: 35px; margin-left: 20px">WORK ORDER UNIT</span>
        <input type="text" name="workorder" id="workorder" value="" style="height: 50px; padding-left: 10px;width: 350px; text-align: left; font-size: 25px; color: #800000; font-weight: bold;">
    </label>
    <label class="input">
        <span style="height: 51px; width: 375px; text-align: left; font-size: 35px; margin-left: 90px">EMPLOYEE</span>
        <input type="text" name="employee" id="employee" value="" style="height: 50px; padding-left: 10px;width: 350px; text-align: left; font-size: 25px; color: #800000; font-weight: bold;">
    </label>
    </div>
    <div style="float: right">
        <input type="SUBMIT" value="CANCEL" name="cancel" id="cancel" class="BUTTON_OK"
               style="height: 115px; width: 300px; text-align: center;
               font-size: 55px; margin: 15px 15px 15px 0; color: #ffffff;
               font-weight: bold; background-color: #90ee90">
    </div>
    <div style="float: right">
        <input type="SUBMIT" value="CONFIRM" name="confirm" id="confirm" class="BUTTON_OK"
               style="height: 115px; width: 300px; text-align: center;
               font-size: 55px; margin: 15px 15px 15px 0; color: #ffffff;
               font-weight: bold; background-color: #EEB4B4">
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
                                            <td align="left" style="font-size: 28px; font-weight: bold; color: #7EB6FF">
                                                <div style="padding-left: 10px">
                                                    CURRENT STATUS
                                                </div>
                                            </td>
                                            <td align="right" vlign="top">
                                                <div style="float: right">
                                                    <input type="button" value="CLICK HERE TO SELECT PHASE MANUALLY" name="select_phase" id="phase"
                                                           style="clear: left; background-color: #7777cc; color: #ffffff; font-size: 18px; font-weight: bold">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="type_aa">
                                                <div id="sp" style="padding-bottom: 8px; padding-left: 5px">
                                                    <span id="sp_1">AWAITING CUTTING</span>
                                                    <span id="sp_2">CUTTING</span>
                                                    <span id="sp_3">AWAITING FABRICATION/CNC</span>
                                                    <span id="sp_4">FABRICATION/CNC</span>
                                                    <span id="sp_5">AWAITING INSTALLATION</span>
                                                    <span id="sp_6">INSTALLATION</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" width="987px" style="border: solid 1px #000000;">
                                    <table width="987px">
                                        <tr>
                                            <td colspan="2">
                                                <div style="float: left; padding-left: 10px; color: #7EB6FF;font-size: 16px;">
                                                    ACTIVE EMPLOYEE: <div id="ep" style="display: inline"></div>
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