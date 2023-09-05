<h2>Please fill up the form.</h2>
<form action="pay.php" METHOD="post">
    <input type="hidden" name="order_amount" value="<?=$_GET['order_amount']?>" >
    <input type="hidden" name="order_description" value="<?=$_GET['order_description']?>" >
    <input type="hidden" name="order_currency" value="USD" >


    <br>order_id:<br> <input type="text" name="order_id" value="<?=rand(1111,9999)?>" ><br>
    <br>card_number:<br> <input type="text" name="card_number" value="4111111111111111" ><br>
    <br><b>exp_month:</b><br> <input type="text" name="card_exp_month" value="01" >
    <br>card_exp_year:<br> <input type="text" name="card_exp_year" value="2025" ><br>
    <br>card_cvv2:<br> <input type="text" name="card_cvv2" value="<?=rand(111,999)?>" ><br>

    <br>first_name:<br> <input type="text" name="payer_first_name" value="Vladimir"><br>
    <br>last_name:<br> <input type="text" name="payer_last_name" value="Kogan"><br>
    <br>middle_name:<br> <input type="text" name="payer_middle_name" value=""><br>
    <br>address:<br> <input type="text" name="payer_address" value="Ahmatovoi 7/15"><br>
    <br>country:<br> <input type="text" name="payer_country" value="UA"><br>
    <br>city:<br> <input type="text" name="payer_city" value="Kiev"><br>
    <br>zip:<br> <input type="text" name="payer_zip" value="Kiev"><br>
    <br>email:<br> <input type="text" name="payer_email" value="2205004@ukr.net"><br>
    <br>phone:<br> <input type="text" name="payer_phone" value="0672205004"><br>
    <br>
    <input type="submit">
</form>