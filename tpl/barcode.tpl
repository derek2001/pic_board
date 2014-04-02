{include file="header_no_user.tpl"}
<script type="text/javascript" src="js/design.js"></script>
<script type="text/javascript" src="js/ajax/jquery-1.6.2.min.js"></script>
{if $error<>''}<br><b><font color="red">{$error}</font></b>{/if}
{literal}
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
</style>
{/literal}


{assign var="module_name" value="Work Order Check In"}
{include file="module_header.tpl"}
<br>

{assign var="table_width" value="1022"}
{assign var="table_headertitle" value="Scan Screen"}
{include file="table_header.tpl"}
<tr><td>
    <form action="barcode.php" method="post" name="barcode" id="barcode-form">
    <div style="float: left">
    <label class="input">
        <span style="height: 51px; width: 351px; text-align: center; font-size: 40px;">WORK ORDER</span>
        <input type="text" name="workorder" id="workorder" value="" style="height: 50px; width: 350px; text-align: center; font-size: 40px; color: #800000; font-weight: bold;">
    </label>
    <label class="input">
        <span style="height: 51px; width: 351px; text-align: center; font-size: 40px;">EMPLOYEE</span>
        <input type="text" name="employee" id="employee" value="" style="height: 50px; width: 350px; text-align: center; font-size: 40px; color: #800000; font-weight: bold;">
    </label>
    </div>
    <div style="float: right">
        <input type="SUBMIT" value="CANCEL" name="cancel" id="cancel" class="BUTTON_OK"
               style="height: 115px; width: 300px; text-align: center;
               font-size: 60px; margin: 15px 15px 15px 0; color: #ffffff;
               font-weight: bold; background-color: #000000">
    </div>
    <div style="float: right">
        <input type="SUBMIT" value="CONFIRM" name="confirm" id="confirm" class="BUTTON_OK"
               style="height: 115px; width: 300px; text-align: center;
               font-size: 60px; margin: 15px 15px 15px 0; color: #ffffff;
               font-weight: bold; background-color: #000000">
    </div>
    </form>
</td></tr>
{include file="table_footer.tpl"}

</td></tr>
<table>

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