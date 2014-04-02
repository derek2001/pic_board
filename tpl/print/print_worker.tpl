<html>
<head>
    <title>STONESEARCH - ERP System</title>
    <meta http-equiv="X-UA-Compatible" content="IE=7" />
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link type="text/css" rel="stylesheet" href="css/{$css_color}.css">
</head>
<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="background: white">

<script type="text/javascript" src="js/design.js"></script>
{if $error<>''}<br><b><font color="red">{$error}</font></b>{/if}

<br>


<center>

    <table  width="750" border="0" id="tab_rows" cellspacing="0" cellpadding="0">

        <tr align="center">
            <td width="18%" class="table" style=" background-color: Gray; border-top: 1px solid #000000;  border-bottom: 1px solid #000000; border-left: 1px solid #000000;">Surname</td>
            <td width="18%" class="table" style=" background-color: Gray;border-top: 1px solid #000000;  border-bottom: 1px solid #000000; border-left: 1px solid #000000;">First Name</td>
            <td width="18%" class="table" style=" background-color: Gray;border-top: 1px solid #000000;  border-bottom: 1px solid #000000; border-left: 1px solid #000000;">Description</td>
            <td width="18%" class="table" style=" background-color: Gray;border-top: 1px solid #000000;  border-bottom: 1px solid #000000; border-left: 1px solid #000000;">ID Punch</td>
            <td width="28%" class="table" style=" background-color: Gray;border-top: 1px solid #000000;  border-bottom: 1px solid #000000; border-left: 1px solid #000000;">Barcode</td>
        </tr>

        {section name=idx loop=$data}
            {assign var="a" value=$data[idx].id_container}

            <tr >
                {if $data[idx].id_punch != ''}
                <td class="small"  style=" padding-left: 3; border-top: 1px none #C0C0C0;  border-bottom: 1px solid #C0C0C0; border-left: 1px solid #C0C0C0;">
                    {if $data[idx].fname != ' ' && $data[idx].fname != ''}{$data[idx].fname}{else} &nbsp; {/if}</td>
                <td class="small"  style=" text-align: center; border-top: 1px none #C0C0C0;  border-bottom: 1px solid #C0C0C0; border-left: 1px solid #C0C0C0;">
                    {if $data[idx].lname != ' ' && $data[idx].lname != ''}{$data[idx].lname}{else} &nbsp; {/if}</td>
                <td class="small"  style=" text-align: center; border-top: 1px none #C0C0C0;  border-bottom: 1px solid #C0C0C0; border-left: 1px solid #C0C0C0;">
                    {if $data[idx].description != ' ' && $data[idx].description != ''}{$data[idx].description}{else} &nbsp; {/if}</td>
                <td class="small"  style=" text-align: center; border-top: 1px none #C0C0C0;  border-bottom: 1px solid #C0C0C0; border-left: 1px solid #C0C0C0;">{$data[idx].id_punch}</td>
                <td class="small"  style=" text-align: center; border-top: 1px none #C0C0C0;  border-bottom: 1px solid #C0C0C0; border-left: 1px solid #C0C0C0;">{$data[idx].id_punch|barcode}</td>
                {/if}
            </tr>

        {/section}


    </table>
</center>

</body>
</html>