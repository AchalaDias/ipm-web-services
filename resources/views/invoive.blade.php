
<html>
    <head>      
    </head>
    <body style="margin: 0">
        <table width="100%" cellspcing="0" cellpadding="0" border="0" style="font-family: Arial, Helvetica, sans-serif; background-color: #ffffff; border-collapse:collapse;">
            <tbody>
                <!--Top area-->
                <tr>
                    <td align="center">
                        <table width="640" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #ffffff; border-collapse: collapse">
                            <tbody>
                                <tr>
                                    <td width="20%">
                                        <table  cellspcing="0" border="0" cellpadding="0" style="background: #ffffff; border-collapse: collapse">
                                            <tbody>
                                                <tr>
                                                    <td style="height: 22px"></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <img src="http://appsy.co/demo/NHRC-mail/ipm-logo.png" >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="height: 23px;"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td width="80%" align="right">
                                        <table  cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #ffffff; border-collapse: collapse">
                                            <tbody>
                                                <tr>
                                                    <td style="height:26px "></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <table width="100%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif;  background: #ffffff; border-collapse: collapse">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="font-family: Arial, Helvetica, sans-serif;font-size: 18px;font-weight: 400;color: #000;padding-bottom: 4px">Support  (+94) 11 2199988</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-family:  Arial, Helvetica, sans-serif;  font-size: 14px;color: rgba(0,0,0,0.56);text-align: right">Feb 1, 2016 01:01:42</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-family:  Arial, Helvetica, sans-serif; font-size: 14px;color: rgba(0,0,0,0.56);text-align: right"><span>Invoice No : </span>INV0002589 - {{str_pad($invoiceId , 4, '0', STR_PAD_LEFT)}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 12px;"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <!--middle area-->
                <tr>
                    <td align="center" style="background: #25c3cc;">
                        <table width="640" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #25c3cc; border-collapse: collapse">
                            <tbody>
                                <tr>
                                    <td style="height: 31px"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #ffffff; border-collapse: collapse">
                                            <tr>
                                                <td colspan="3" style="padding: 20px"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 40px;"></td>
                                                <td>
                                                    <table width="100%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #ffffff; border-collapse: collapse">
                                                        <tbody>
                                                            <tr>
                                                                <td style="color:#020202;font-weight: bold;font-size: 14px;font-family:  Arial, Helvetica, sans-serif;padding-bottom: 22px;">Hi {{$username}},</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="color:#333333;font-weight: 400;font-size: 14px;font-family:  Arial, Helvetica, sans-serif;">
                                                                    This is to confirm your payment of Rs {{$UserAmount}}/= for the registration at National HRConference 2016 organized by IPM Sri Lanka <a style="color:rgba(51, 51, 51, 0.89);text-decoration: none; font-weight: 600; " href="mailto:ipminfo@ipmlk.org">(ipminfo@ipmlk.org)</a> .
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td style="width: 40px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="height: 40px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 40px;"></td>
                                                <td>
                                                    <table width="100%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #ffffff; border-collapse: collapse;border: 1px solid #d5d5d5">
                                                        <thead style="background: #c0c0c0">
                                                            <tr style="color: #ffffff;font-size: 12px;font-family: Arial, Helvetica, sans-serif;text-align: center">
                                                                <th style="width: 203px; padding: 10px 0 10px 20px; border: 1px solid #d5d5d5; text-align: left;">Description</th>
                                                                <th style="width: 118px; padding: 10px 0; border: 1px solid #d5d5d5;">No of participants</th>
                                                                <th style="width: 118px; padding: 10px 0; border: 1px solid #d5d5d5;">Charge per Head</th>
                                                                <th style="width: 118px; padding: 10px 20px 10px 0; border: 1px solid #d5d5d5;  text-align: right">Sub Total</th>

                                                            </tr>
                                                        </thead>
                                                        <tbody style="color: #333333; font-size: 12px; font-family: Arial, Helvetica, sans-serif; text-align: center">
                                                            <tr style="vertical-align: top; color: #333333;">
                                                                <td style="width: 203px; padding: 10px 0 0 20px; border-right: 1px solid #d5d5d5; text-align: left;">NHRC 2016 - Participant fee (Non Member)</td>
                                                                <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">{{$participantCount}}</td>
                                                                <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">{{$chargeperHead}} LKR</td>
                                                                <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">{{$firstAmount}}.00 LKR</td>
                                                            </tr>
                                                            <tr style="vertical-align: top; color: #333333;">
                                                                <td style="width: 203px; padding: 10px 0 0 20px; border-right: 1px solid #d5d5d5; text-align: left;">Research Symposium </td>
                                                                <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">{{$researchCount}}</td>
                                                                <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">3000.00 LKR</td>
                                                                <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; border-bottom: 1px solid #d5d5d5; text-align: right">{{$researchAmount}}.00 LKR</td>
                                                            </tr>
                                                           
                                                            <tr style="vertical-align: top; color: #333333;">
                                                                <td style="width: 203px; padding: 10px 0 0 20px; border-right: 1px solid #d5d5d5; text-align: left;">&nbsp;</td>
                                                                <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; text-align: right">{{$secondTotal}}.00 LKR</td>
                                                            </tr>
                                                            <tr style="vertical-align: top; color: #333333;">
                                                                <td style="width: 203px; padding: 10px 0 0 20px; border-right: 1px solid #d5d5d5; text-align: left;">NBT 2%</td>
                                                                <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; border-bottom: 1px solid #d5d5d5; text-align: right">{{$NBT}}.00 LKR</td>
                                                            </tr>

                                                            <tr style="vertical-align: top; color: #333333;background: #f5f5f5">
                                                                <td style="width: 203px; padding: 10px 0 0 20px; text-align: left; text-transform: uppercase; font-weight: bold">Total</td>
                                                                <td style="width: 118px; padding: 10px 20px 0 0; text-align: right">&nbsp;</td>
                                                                <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; border-bottom: 1px solid #d5d5d5; text-align: right; text-transform: uppercase; font-weight: bold">{{$finalAmount}}.00 LKR</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td style="width: 40px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="height: 20px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 40px;"></td>
                                                <td style="color:#333333;font-weight: bold;font-size: 12px;font-family:  Arial, Helvetica, sans-serif; font-weight: 400;">NBT Reg. No : 409060870-9000 </td>
                                                <td style="width: 40px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="height: 22px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 40px;"></td>
                                                <td>
                                                    <table width="100%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #ffffff; border-collapse: collapse">
                                                        <tbody>

                                                            <tr style="vertical-align: top">
                                                                <td style="color: #333333; font-size: 14px; font-weight: bold; width: 33%">Contact Infomation :</td>
                                                                <td style="color: #333333; font-size: 14px; font-weight: 400; width: 67%">
                                                                   {{$company_contact_title}}{{ $company_contact_name}},
                                                                    <br>Designation:{{$company_contact_designation}},
                                                                    <br>{{$company_contact_email}},
                                                                    <br>{{$company_contact_phone}}.
                                                                   
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td style="width: 40px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="height: 40px;"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 30px"></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <!--bottom area-->
                <tr>
                    <td align="center" style=" background: #090924;">
                        <table width="640" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #090924; border-collapse: collapse">
                            <tbody>
                                <tr>
                                    <td>
                                        <table width="100%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #090924; border-collapse: collapse">
                                            <tbody>
                                                <tr>
                                                    <td style="height: 22px;"></td>
                                                </tr>
                                                <tr>
                                                    <td style="color: #ffffff; font-weight: bold; font-size: 24px; text-align: center"><span>Invoice No : </span>INV0002589 - {{str_pad($invoiceId , 4, '0', STR_PAD_LEFT)}}</td>
                                                    <td><img src='QRpng/test.png' /></td>
                                                </tr>
                                                <tr>
                                                    <td style="height: 40px;"></td>

                                                    
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #090924; border-collapse: collapse">
                                            <tbody style="color: #676773; font-weight: 400; font-size: 14px; text-align: left">
                                                <tr >
                                                    <td style="padding-bottom: 20px;color: #676773;">By using these products, you agree that you are bound by the <a href="#a" style="color: #cdcdcd; text-decoration: none;border-bottom: 2px solid #84848a;">Terms of Service</a> and <a href="#a" style="color: #cdcdcd; text-decoration: none;border-bottom: 2px solid #84848a;"> Privacy Policy.</a> Learn more about our  <a href="#a" style="color: #cdcdcd; text-decoration: none;border-bottom: 2px solid #84848a;"> Refund Policy</a>.</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding-bottom: 20px; color: #676773;">
                                                        Keep your Invoice number (shown above) for future reference. You will need to refer to this number if you need customer service from Institute of Personnel Management Sri Lanka.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding-bottom: 20px; color: #676773;">
                                                        Please do not reply to this email. Email sent to this address cannot be answered.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="color: #676773;"> Copyright &copy; 1999-2016 PayPal. All rights reserved. </td>
                                                </tr>
                                                <tr>
                                                    <td style="height: 60px;"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
