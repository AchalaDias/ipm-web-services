<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\QR\BarcodeQR;
use App\QR\qrlib;
use App\MailClass\PHPMailer;
use Vsmoraes\Pdf\Pdf;
use Mail;


class DashboardServices extends Controller
{


    private $pdf;

    public function __construct(Pdf $pdf)
    {
        $this->pdf = $pdf;
    }



    public function registrationAndPayments(Request $request){


        $PaymentDetails = DB::select("select (select k.packagers_name from packagers k where k.packagers_id = a.company_packagers)
	as 'name' ,
	SUM((select count(*) from company where  company_packagers = a.company_packagers))
	as 'registered',
	SUM((select count(*) from company where company_paymant_status != 0  and company_packagers = a.company_packagers) )
	as 'paid'
	from  company a
	group by a.company_packagers");


        return response()->json(['data' =>   $PaymentDetails]);

    }

    public function targetVsAchievment(Request $request){





    }

    public function registrationVsAttendance(Request $request){


        $result = DB::select("  	select (select k.packagers_name from packagers k where k.packagers_id = a.company_packagers) as 'name' ,

		SUM((select count(p.participant_id) from participant p where p.participant_company = a.company_id and attendance_status = 1))
				as 'registered',
			    SUM((select count(*) from company where company_paymant_status != 0  and company_packagers = a.company_packagers) )
       			as 'attendance'
				from  company a
				group by a.company_packagers");


        return response()->json(['data' =>   $result]);



    }

    /*
    public function CompanyPayment(Request $request){


    	$payment_method = $request->input("payment_method");
    	$cheque_no      = $request->input("cheque_no");
    	$bank           = $request->input("bank");
    	$branch         = $request->input("branch");
    	$amount         = (float)$request->input("amount");
    	$company_id     = $request->input("company_id");
    	$count 			= (int)$request->input("count");

    	$IndividualAmonut = (float)($amount/$count);


    	$Pids = DB::select("select participant_id from participant where participant_company = '$company_id'");


    	foreach ($Pids as $v) {

    	$id = $v->participant_id;

    	$res1 = DB::statement(DB::raw("INSERT INTO payments(participant_id,payment_method,payment_role,cheque_no,bank,branch,amount) values('$id','$payment_method','company','$cheque_no','$bank','$branch','$IndividualAmonut')"));

		}

    	$res2 = DB::statement(DB::raw("UPDATE company SET company_paymant_type = '$payment_method',company_paymant_amount = '$amount',company_paymant_status = 1 where company_id = '$company_id'"));

    	if($res2){
    		 return response()->json(['message' => 'success' ]);
    	}
    	else{

    		 return response()->json(['message' => 'fail' ]);

    	}

    }
*/

    public function CompanyPayment(Request $request){


        $payment_method = $request->input("payment_method");
        $cheque_no      = $request->input("cheque_no");
        $bank           = $request->input("bank");
        $branch         = $request->input("branch");
        $amount         = (float)$request->input("amount");
        $company_id     = $request->input("company_id");
        $count                                  = (int)$request->input("count");

        $IndividualAmonut = (float)($amount/$count);


        $Pids   = DB::select("select * from participant where participant_company = '$company_id'");
        $CompanyDetails = DB::select("select * from company where company_id = '$company_id'");


        $CompanypakageId = $CompanyDetails[0]->company_packagers;
        $researchCount = 0;
        $invoiceTotal = 0;
        $invoiceResearchAmount = 0;;
        $chargeperHead = 0;



        foreach ($Pids as $v) {

            $id = $v->participant_id;

            $res1 = DB::statement(DB::raw("INSERT INTO payments(participant_id,payment_method,payment_role,cheque_no,bank,branch,amount) values('$id','$payment_method','company','$cheque_no','$bank','$branch','$IndividualAmonut')"));

            $researchStats = $v->participant_research_status;
            if($researchStats == 1){
                $researchCount++;
            }

        }

        $res2 = DB::statement(DB::raw("UPDATE company SET company_paymant_type = '$payment_method',company_paymant_amount = '$amount',company_paymant_status = 1 where company_id = '$company_id'"));



        if($CompanypakageId == 1){

            $invoiceTotal = $count*16000;
            $invoiceResearchAmount = $researchCount*3000;
            $chargeperHead = 16000.00;
        }
        if($CompanypakageId == 2){

            $invoiceTotal = $count*12000;
            $invoiceResearchAmount = $researchCount*3000;
            $chargeperHead = 12000.00;
        }
        if($CompanypakageId == 3){

            $invoiceTotal = $count*12000;
            $invoiceResearchAmount = $researchCount*3000;
            $chargeperHead = 12000.00;
        }




        $company_contact_title = $CompanyDetails[0]->company_contact_title;
        $company_contact_name = $CompanyDetails[0]->company_contact_name;
        $company_contact_designation = $CompanyDetails[0]->company_contact_designation;
        $company_contact_email = $CompanyDetails[0]->company_contact_email;
        $company_contact_phone = $CompanyDetails[0]->company_contact_phone;



        $company_email = $CompanyDetails[0]->company_email;


        $PNG_TEMP_DIR = "../public/QRpng/";
        $filename = $PNG_TEMP_DIR.$company_id.'.png';

        $QRtext = "company_id:".$company_id."|participant_count:".$count."|package_id:";

        $qr = new BarcodeQR();
        $qr->text($QRtext);
        $qr->draw(100, $filename);



        if($count >= 5){


            $secondTotal = $invoiceTotal+$invoiceResearchAmount;
            $discount    = $secondTotal*0.05;
            $thirdTotal  = $secondTotal - $discount;
            $NBT         = $thirdTotal*0.02;
            $finalAmount = $thirdTotal + $NBT;



            Mail::send('invoiveD',['imgPath'=>$filename ,'username'=> $CompanyDetails[0]->company_name,'firstAmount'=>$invoiceTotal,'secondTotal'=>$secondTotal,'participantCount'=>$count,'researchCount'=>$researchCount,'researchAmount'=> $invoiceResearchAmount,'discountAmount'=>$discount,'thirdTotal'=>$thirdTotal,'NBT'=>$NBT,'finalAmount'=>$finalAmount,'UserAmount'=>$amount,'chargeperHead'=>$chargeperHead,'invoiceId'=>$CompanyDetails[0]->company_id,'company_contact_title'=>$company_contact_title,'company_contact_name'=>$company_contact_name,'company_contact_designation'=>$company_contact_designation,'company_contact_email'=>$company_contact_email,'company_contact_phone'=>$company_contact_phone], function($message)  use ($company_email){


                $message->to($company_email,'IPM')->subject('IPM payment invoice');




            });


            $this->InvoicePDFD($filename,$CompanyDetails[0]->company_name,$CompanyDetails[0]->company_id,$invoiceTotal,$amount,$invoiceTotal,$secondTotal,$count,$researchCount,$invoiceResearchAmount,$NBT,$finalAmount,$chargeperHead,$company_contact_title,$company_contact_name,$company_contact_designation,$company_contact_email,$company_contact_phone,$discount,$thirdTotal);

            $pdfPath = 'PDF/'.$CompanyDetails[0]->company_id.'.pdf';

            return response()->json(['message' => 'success','PDF_file'=> $pdfPath]);

        }
        else{

            $secondTotal = $invoiceTotal+$invoiceResearchAmount;
            $NBT         = $secondTotal*0.02;
            $finalAmount = $secondTotal + $NBT;

            Mail::send('invoive',['imgPath'=>'test.png' ,'username'=> $CompanyDetails[0]->company_name,'firstAmount'=>$invoiceTotal,'secondTotal'=>$secondTotal,'participantCount'=>$count,'researchCount'=>$researchCount,'researchAmount'=> $invoiceResearchAmount,'NBT'=>$NBT,'finalAmount'=>$finalAmount,'UserAmount'=>$amount,'chargeperHead'=>$chargeperHead, 'invoiceId'=>$CompanyDetails[0]->company_id,'company_contact_title'=>$company_contact_title,'company_contact_name'=>$company_contact_name,'company_contact_designation'=>$company_contact_designation,'company_contact_email'=>$company_contact_email,'company_contact_phone'=>$company_contact_phone],
                       function($message) use ($company_email){

                           $message->to($company_email,'IPM')->subject('IPM payment invoice');

                       });

    $this->InvoicePDF($filename,$CompanyDetails[0]->company_name,$CompanyDetails[0]->company_id,$invoiceTotal,$amount,$invoiceTotal,$secondTotal,$count,$researchCount,$invoiceResearchAmount,$NBT,$finalAmount,$chargeperHead,$company_contact_title,$company_contact_name,$company_contact_designation,$company_contact_email,$company_contact_phone);


            $pdfPath = 'PDF/'.$CompanyDetails[0]->company_id.'.pdf';

            return response()->json(['message' => 'success','PDF_file'=> $pdfPath]);

        }



    }



    /*
    public function IndividualPayment(Request $request){



    	$payment_method = $request->input("payment_method");
    	$cheque_no      = $request->input("cheque_no");
    	$bank           = $request->input("bank");
    	$branch         = $request->input("branch");
    	$amount         = $request->input("amount");
    	$participant_id = $request->input("participant_id");
    	$company_id     = $request->input("company_id");


    	$res1 =   DB::statement(DB::raw("INSERT INTO payments(participant_id,payment_method,payment_role,cheque_no,bank,branch,amount) values('$participant_id','$payment_method','indv','$cheque_no','$bank','$branch','$amount')"));


    	$res2 =   DB::statement(DB::raw("UPDATE company SET company_paymant_type = '$payment_method',company_paymant_amount = '$amount',company_paymant_status = 1 where company_id = '$company_id'"));


    	if($res1 == true || $res2 == true){

    		 return response()->json(['message' => "success"]);
    	}
    	else{

    		 return response()->json(['message' => "fail"]);
    	}


    }

    */



    public function IndividualPayment(Request $request){



        $payment_method = $request->input("payment_method");
        $cheque_no      = $request->input("cheque_no");
        $bank           = $request->input("bank");
        $branch         = $request->input("branch");
        $amount         = $request->input("amount");
        $participant_id = $request->input("participant_id");
        $company_id     = $request->input("company_id");

        $count = 1;
        $CompanyDetails = DB::select("select * from company where company_id = '$company_id'");
        $Pids   = DB::select("select * from participant where participant_company = '$company_id'");

        $CompanypakageId = $CompanyDetails[0]->company_packagers;
        $researchCount = 0;
        $invoiceTotal = 0;
        $invoiceResearchAmount = 0;;
        $chargeperHead = 0;


        $res1 =   DB::statement(DB::raw("INSERT INTO payments(participant_id,payment_method,payment_role,cheque_no,bank,branch,amount) values('$participant_id','$payment_method','indv','$cheque_no','$bank','$branch','$amount')"));


        $res2 =   DB::statement(DB::raw("UPDATE company SET company_paymant_type = '$payment_method',company_paymant_amount = '$amount',company_paymant_status = 1 where company_id = '$company_id'"));




        if($CompanypakageId == 1){

            $invoiceTotal = $count*16000;
            $invoiceResearchAmount = $researchCount*3000;
            $chargeperHead = 16000.00;
        }
        if($CompanypakageId == 2){

            $invoiceTotal = $count*12000;
            $invoiceResearchAmount = $researchCount*3000;
            $chargeperHead = 12000.00;
        }
        if($CompanypakageId == 3){

            $invoiceTotal = $count*12000;
            $invoiceResearchAmount = $researchCount*3000;
            $chargeperHead = 12000.00;
        }



        $company_contact_title = $CompanyDetails[0]->company_contact_title;
        $company_contact_name = $CompanyDetails[0]->company_contact_name;
        $company_contact_designation = $CompanyDetails[0]->company_contact_designation;
        $company_contact_email = $CompanyDetails[0]->company_contact_email;
        $company_contact_phone = $CompanyDetails[0]->company_contact_phone;

        $participant_email = $Pids[0]->participant_email;
        $company_email = $CompanyDetails[0]->company_email;



        $PNG_TEMP_DIR = "../public/QRpng/";
        $filename = $PNG_TEMP_DIR.$company_id.'.png';

        $QRtext = "company_id:".$company_id."|participant_count:".$count."|package_id:".$CompanypakageId;

        $qr = new BarcodeQR();
        $qr->text($QRtext);
        $qr->draw(100, $filename);

        $secondTotal = $invoiceTotal+$invoiceResearchAmount;
        $NBT         = $secondTotal*0.02;
        $finalAmount = $secondTotal + $NBT;

        Mail::send('invoive',['imgPath'=>$filename ,'username'=> $CompanyDetails[0]->company_name,'firstAmount'=>$invoiceTotal,'secondTotal'=>$secondTotal,'participantCount'=>$count,'researchCount'=>$researchCount,'researchAmount'=> $invoiceResearchAmount,'NBT'=>$NBT,'finalAmount'=>$finalAmount,'UserAmount'=>$amount,'chargeperHead'=>$chargeperHead,'invoiceId'=>$CompanyDetails[0]->company_id,'company_contact_title'=>$company_contact_title,'company_contact_name'=>$company_contact_name,'company_contact_designation'=>$company_contact_designation,'company_contact_email'=>$company_contact_email,'company_contact_phone'=>$company_contact_phone], function($message) use ($company_email,$participant_email){

            $message->to($company_email,'IPM')->to($participant_email,'IPM')->subject('IPM payment invoice');

        });


        $this->InvoicePDF($filename,$CompanyDetails[0]->company_name,$CompanyDetails[0]->company_id,$invoiceTotal,$amount,$invoiceTotal,$secondTotal,$count,$researchCount,$invoiceResearchAmount,$NBT,$finalAmount,$chargeperHead,$company_contact_title,$company_contact_name,$company_contact_designation,$company_contact_email,$company_contact_phone);

        $pdfPath = 'PDF/'.$CompanyDetails[0]->company_id.'.pdf';




        if($res1 == true && $res2 == true){

            return response()->json(['message' => 'success','PDF_file'=> $pdfPath]);
        }
        else{

            return response()->json(['message' => "fail"]);
        }


    }


    public function IndividualsFromCompany(Request $rquest){

        $company_id = $rquest->input("companyId");

        $result = DB::select("select * from participant where participant_company = '$company_id'");

        return response()->json(['data' => $result]);


    }

    public function CompanyList(Request $request){

        $result = DB::select("select a.*,
      (select  b.packagers_name from packagers b where b.packagers_id = a.company_packagers) as packagers_name,
      (select count(*) from  participant b where b.participant_company = a.company_id) as user_count,
       (select  b.packagers_description from packagers b where b.packagers_id = a.company_packagers) as packagers_description,
		(select  b.description_amount from packagers b where b.packagers_id = a.company_packagers) as description_amount
       from company a where a.company_com_indiv =0 ");


        return $result;

    }

    public function IndividualList(Request $request){

        $individual = DB::select("select a.*,
     	(select (select  b.packagers_name from packagers b where b.packagers_id = c.company_packagers) as abc from company c where c.company_id = a.participant_company ) as packagers_name
,(select company_paymant_status from company where company_id  = a.participant_company) as payment_status,
	(select (select  b.packagers_description from packagers b where b.packagers_id = c.company_packagers) as abc from company c where c.company_id = a.participant_company ) as packagers_description,
	(select (select  b.description_amount from packagers b where b.packagers_id = c.company_packagers) as abc from company c where c.company_id = a.participant_company ) as amount_disply
from participant a
 where a.participant_company in (select company_id from company where company_com_indiv = 0 )");

        $company = $this->CompanyList($request);

        return response()->json(['individual' =>   $individual, 'comapany' => $company]);

    }


    public function PaymentProcess(Request $request){


        $payment_method = "cash";
        $cheque_no = "null";
        $bank = "null";
        $branch = "null";
        $amount = 23905;


        /*$res =   DB::statement(DB::raw("INSERT INTO payments(payment_method,cheque_no,bank,branch,amount) values('$payment_method',$cheque_no,'$bank','$branch',$amount)")); */

        $PNG_TEMP_DIR = "C:\/xampp\htdocs\IPMwebServices\public\QRpng\/";

        $filename = $PNG_TEMP_DIR.'test.png';



        $qr = new BarcodeQR();


        $qr->text("IPM test data");

        $qr->draw(200, $filename);

        echo '<img src="'.$filename.'" /><hr/>';




        $mailer_fogot = new PHPMailer();
        $mailer_fogot->IsSMTP();          // set mailer to use SMTP
        $mailer_fogot->Host = "smtp.hnbassurance.com";  // specify main and backup server
        $mailer_fogot->SMTPAuth = true;     // turn on SMTP authentication
        $mailer_fogot->Username = "misreports";  // SMTP username
        $mailer_fogot->Password = "Water@1234"; // SMTP password

        $mailer_fogot->From = "ipm@gmail.com";
        $mailer_fogot->FromName = "Payment details";



        $mailer_fogot->AddAddress("achala.dias@hnbassurance.com");

        $msgBody = "<img src='".$filename."' /><hr/>";
        $mail_body_fog=$msgBody;

        $mailer_fogot->Subject = "ipm payment details";
        $mailer_fogot->Body=$msgBody;
        $mailer_fogot->AltBody = "";

        if(!$mailer_fogot->Send())
        {
            echo "Message could not be sent. <p>";
            echo "Mailer Error: " . $mailer_fogot->ErrorInfo;
            exit;
        }else{

            echo "Sucess!!";

        }


    }


    public function showAgenda(Request $request){

        //$date = $request->input("agendaDate");

        $arr = DB::select(DB::raw("select item_date_occurs from agenda group by item_date_occurs"));

        $result = array();
        foreach ($arr as $a) {

            $temp = DB::select(DB::raw("select a.*,
        DATE_FORMAT(a.item_time,'%l:%i %p') as start_time,
        IFNULL((select b.speaker_name from speakers b where b.speaker_id = a.speaker_id),'Not Assigned') as speaker_name
        from agenda a where a.item_date_occurs = '".$a->item_date_occurs."' order by a.item_time asc"));

            $tempArr = array('date' =>$a->item_date_occurs, 'data'=> $temp);
            array_push($result , $tempArr);
        }


        //	$result = DB::select("select * from agenda where  item_date = STR_TO_DATE('$date', '%d/%m/%Y %r')");


        return response()->json(['data' => $result]);
    }

    public function saveAgenda(Request $request){



        $name        = $request->input("name");
        $description = $request->input("desc");
        $time        = $request->input("time");



        DB::table('agenda')->insert(
            array('item_name' =>$name,'item_description' => $description ,'item_time'=> $time, 'speaker_id' => $request->input("speaker"),
                  'item_date_occurs'=>$request->input("dateOccurs"))
        );

        /*
     	$res1 = DB::statement("insert into agenda(item_name,item_description,item_time,item_date)
			values('$name','$description','$time',STR_TO_DATE('$date', '%d/%m/%Y %r'))
			"); */


        /*	if($res1){
     		 return response()->json(['message' => "success"]);
     	}else{

     		return response()->json(['message' => "fail"]);
     	}
*/
    }

    public function updateAgenda(Request $request){

        $id 		 = $request->input("itemId");
        $name        = $request->input("name");
        $description = $request->input("desc");
        $time        = $request->input("time");
        $date        = $request->input("dateOccurs");

        /*
     	$res1 = DB::statement(DB::raw("UPDATE agenda set item_name = '$name',item_description = '$description',item_time = '$item_time',
     		 item_date = STR_TO_DATE('$date', '%d/%m/%Y %r') where id = '$id'"));

       DB::table('agenda')->insert(
             array('item_name' =>$name,'item_description' => $description ,'item_time'=> $time, 'speaker_id' => $request->input("speaker"),
             'item_date_occurs'=>$request->input("dateOccurs"))
         );

*/

        DB::table('agenda')
            ->where('id', $request->input("id"))
            ->update(
            array('item_name' =>$name,'item_description' => $description ,'item_time'=> $time, 'speaker_id' => $request->input("speaker"),
                  'item_date_occurs'=>$request->input("dateOccurs"))
        );


    }

    public function deleteAgendaItemByID(Request $request){

        $id 		 = $request->input("itemId");
        $res1 = DB::statement(DB::raw("DELETE FROM agenda where id = '$id'"));

        if($res1){
            return response()->json(['message' => "success"]);
        }else{

            return response()->json(['message' => "fail"]);
        }

    }

    public function deleteAllAgendaItemsFromDate(Request $request){

        $date 		 = $request->input("date");
        $res1 = DB::statement(DB::raw("DELETE FROM agenda where item_date = STR_TO_DATE('$date', '%d/%m/%Y %r')"));

        if($res1){
            return response()->json(['message' => "success"]);
        }else{

            return response()->json(['message' => "fail"]);
        }


    }

    public function addSpeakers(Request $request){

        $name        = $request->input("speakerName");
        $description = $request->input("speakerDescription");
        $image_path  = $request->input("speakerImage");
        $country     = $request->input("speakerCountry");


        $res1 = DB::statement(DB::raw("INSERT INTO speakers(speaker_name,speaker_description,speaker_datetime,speaker_image,speaker_country) values('$name','$description',now(),'$image_path','$country')"));


        if($res1){
            return response()->json(['message' => "success"]);
        }else{

            return response()->json(['message' => "fail"]);
        }


    }

    public function UpdateSpeakers(Request $request){

        $id          = $request->input("speakerId");
        $name        = $request->input("speakerName");
        $description = $request->input("speakerDescription");
        $image_path  = $request->input("speakerImage");
        $country     = $request->input("speakerCountry");


        /*$res1 = DB::statement(DB::raw("INSERT INTO speakers(speaker_name,speaker_description,speaker_datetime,speaker_imgae,speaker_country) values('$name','$description',now(),$image_path,'$image_path','$country')"));*/

        $res1 = DB::statement(DB::raw("UPDATE speakers set speaker_description = '$description',speaker_name = '$name',speaker_datetime = now(),
     		speaker_country = '$country' where speaker_id = '$id'"));


        if($res1){
            return response()->json(['message' => "success"]);
        }else{

            return response()->json(['message' => "fail"]);
        }


    }

    public function deleteSpeaker(Request $request){

        $id    = $request->input("speakerId");

        $res1 = DB::statement(DB::raw("DELETE FROM speakers where speaker_id = '$id'"));


        if($res1){
            return response()->json(['message' => "success"]);
        }else{

            return response()->json(['message' => "fail"]);
        }

    }

    public function showAllSpeakers(Request $request){

        $result = DB::select("select * from speakers ");
        $arr = array();
        foreach($result as $r){

            $temp = DB::select(DB::raw("select a.* ,  DATE_FORMAT(a.item_time,'%l:%i %p') as start_time from agenda a where a.speaker_id = '".$r->speaker_id."'"));
            $rating = DB::select(DB::raw("select count(*) as cnt, IFNULL(sum(a.rate_amount),0) as tot
         from speakerrating a where a.speaker_id  = '".$r->speaker_id."'"));

            if($rating[0]->cnt != 0){
                $avg = $rating[0]->tot / $rating[0]->cnt;
            }else{
                $avg = 0;
            }
            $chart = array(); // DB::table("speakerrating")->where("speaker_id",$r->speaker_id)->pluck('rate_amount');
            for($i=0;$i<6;$i++){

                //$t = DB::table("speakerrating")->where("speaker_id",$r->speaker_id)->where(DB::raw("ROUND(rate_amount) = 3"))->count('rate_amount');

                $t = DB::select(DB::raw("SELECT count(*) as cnt FROM `speakerrating` WHERE round (`rate_amount`) = '".$i."'"));

                array_push($chart,$t[0]->cnt);
            }

            //  $chart = DB::select(DB::raw(" select rate_amount from speakerrating   where speaker_id  = '".$r->speaker_id."'"))->pluck('rate_amount');
            $r->agenda = $temp;
            $r->rating = $avg;
            $r->chart = $chart;
            $r->labels = array("No Star", "1 Star", '2 Stars' , "3 Stars", "4 stars", "5 Stars");
            array_push($arr,$r);
        }

        return response()->json(['data' => $arr]);
    }

    public function showSpeakerDetailsById(Request $request){

        $id  = $request->input("speakerId");

        $result = DB::select("select * from speakers where speaker_id = '$id'");

        return response()->json(['data' => $result]);
    }


    public function ratingAmount(Request $request){

        $result = DB::select("select a.*,sum(a.rate_amonut),count(a.participant_id) from speakerRating a group by a.speaker_id");

        return response()->json(['data' => $result]);


    }

    public function userLoginDetails(Request $request){

        $username = $request->input("username");
        $password = $request->input("password");

        $result = DB::select("select * from user where user_username = '$username' and user_password = '$password'");

        return response()->json(['data' => $result]);

    }


    public function InvoicePDFD($filename,$username,$invoiceId,$firstAmount,$UserAmount,$invoiceTotal,$secondTotal,$count,$researchCount,$invoiceResearchAmount,$NBT,$finalAmount,$chargeperHead,$company_contact_title,$company_contact_name,$company_contact_designation,$company_contact_email,$company_contact_phone,$discountAmount,$thirdTotal){

        $html = '
    <html>
        <head>
        </head>
        <body style="margin: 0">
            <table width="90%" cellspcing="0" cellpadding="0" border="0" style="font-family: Arial, Helvetica, sans-serif; background-color: #ffffff; border-collapse:collapse;">
                <tbody>
                    <!--Top area-->
                    <tr>
                        <td align="center">
                            <table width="600" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #ffffff; border-collapse: collapse">
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
                                                            <img src="IPMLogo/ipm-logo.png" >
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
                                                                        <td style="font-family:  Arial, Helvetica, sans-serif; font-size: 14px;color: rgba(0,0,0,0.56);text-align: right"><span>Invoice No : </span>INV0002589 - '.str_pad($invoiceId , 4, '0', STR_PAD_LEFT).'</td>
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
                            <table width="640"  cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #25c3cc; border-collapse: collapse">
                                <tbody>
                                    <tr>
                                        <td style="height: 31px"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width="84%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #ffffff; border-collapse: collapse">
                                                <tr>
                                                    <td colspan="3" style="padding: 20px"></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 40px;"></td>
                                                    <td>
                                                        <table width="100%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #ffffff; border-collapse: collapse">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="color:#020202;font-weight: bold;font-size: 14px;font-family:  Arial, Helvetica, sans-serif;padding-bottom: 22px;">Hi '.$username.',</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="color:#333333;font-weight: 400;font-size: 14px;font-family:  Arial, Helvetica, sans-serif;">
                                                                        This is to confirm your payment of Rs '.$UserAmount.'/= for the registration at National HRConference 2016 organized by IPM Sri Lanka <a style="color:rgba(51, 51, 51, 0.89);text-decoration: none; font-weight: 600; " href="mailto:ipminfo@ipmlk.org">(ipminfo@ipmlk.org)</a> .
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
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">'.$count.'</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">'.$chargeperHead.' LKR</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">'.$firstAmount.'.00 LKR</td>
                                                                </tr>
                                                                <tr style="vertical-align: top; color: #333333;">
                                                                    <td style="width: 203px; padding: 10px 0 0 20px; border-right: 1px solid #d5d5d5; text-align: left;">Research Symposium </td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">'.$researchCount.'</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">3000.00 LKR</td>
                                                                    <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; border-bottom: 1px solid #d5d5d5; text-align: right">'.$invoiceResearchAmount.'.00 LKR</td>
                                                                </tr>

                                                                <tr style="vertical-align: top; color: #333333;">
                                                                    <td style="width: 203px; padding: 10px 0 0 20px; border-right: 1px solid #d5d5d5; text-align: left;">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; text-align: right">'.$secondTotal.'.00 LKR</td>
                                                                </tr>
                                                                <tr style="vertical-align: top; color: #333333;">
                                                                    <td style="width: 203px; padding: 10px 0 0 20px; border-right: 1px solid #d5d5d5; text-align: left;">Discount 5%</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; border-bottom: 1px solid #d5d5d5; text-align: right">'.$discountAmount.'.00 LKR</td>
                                                                </tr>
                                                                <tr style="vertical-align: top; color: #333333;">
                                                                    <td style="width: 203px; padding: 10px 0 0 20px; border-right: 1px solid #d5d5d5; text-align: left;">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; text-align: right">'.$thirdTotal.'.00 LKR</td>
                                                                </tr>
                                                                <tr style="vertical-align: top; color: #333333;">
                                                                    <td style="width: 203px; padding: 10px 0 0 20px; border-right: 1px solid #d5d5d5; text-align: left;">NBT 2%</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; border-bottom: 1px solid #d5d5d5; text-align: right">'.$NBT.'.00 LKR</td>
                                                                </tr>

                                                                <tr style="vertical-align: top; color: #333333;background: #f5f5f5">
                                                                    <td style="width: 203px; padding: 10px 0 0 20px; text-align: left; text-transform: uppercase; font-weight: bold">Total</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; border-bottom: 1px solid #d5d5d5; text-align: right; text-transform: uppercase; font-weight: bold">'.$finalAmount.'.00 LKR</td>
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
                                                                       '.$company_contact_title.$company_contact_name.',
                                                                        <br>Designation:'.$company_contact_designation.',
                                                                        <br>'.$company_contact_email.',
                                                                        <br>'.$company_contact_phone.'.

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
                            <table width="600" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #090924; border-collapse: collapse">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table width="94%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #090924; border-collapse: collapse">
                                                <tbody>
                                                    <tr>
                                                        <td style="height: 22px;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: #ffffff; font-weight: bold; font-size: 24px; text-align: center"><span>Invoice No : </span>INV0002589 - '.str_pad($invoiceId , 4, '0', STR_PAD_LEFT).'</td>
                                                        <td><img src='.$filename.' /></td>
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
                                            <table width="94%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #090924; border-collapse: collapse">
                                                <tbody style="color: #676773; font-weight: 400; font-size: 14px; text-align: left">
                                                    <tr >
                                                        <td style="padding-bottom: 20px;color: #676773;">By using these products, you agree that you are bound by the <a href="#a" style="color: #cdcdcd; text-decoration: none;border-bottom: 2px solid #84848a;">Terms of Service</a> and <a href="#a" style="color: #cdcdcd; text-decoration: none;border-bottom: 2px solid #84848a;"> Privacy Policy.</a> Learn more about our  <a href="#a" style="color: #cdcdcd; text-decoration: none;border-bottom: 2px solid #84848a;"> Refund Policy</a>.</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding-bottom: 20px; color: #676773;">
                                                            Keep your Invoice number (shown above) for future reference. You will need to refer to this number if you need<br>customer service from Institute of Personnel Management Sri Lanka.
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
    ';


        return $this->pdf
            ->load($html)
            ->filename('PDF/'.$invoiceId.'.pdf')
            ->output();


    }


    public function InvoicePDF($filename,$username,$invoiceId,$firstAmount,$UserAmount,$invoiceTotal,$secondTotal,$count,$researchCount,$invoiceResearchAmount,$NBT,$finalAmount,$chargeperHead,$company_contact_title,$company_contact_name,$company_contact_designation,$company_contact_email,$company_contact_phone){

        $html = '
    <html>
        <head>
        </head>
        <body style="margin: 0">
            <table width="90%" cellspcing="0" cellpadding="0" border="0" style="font-family: Arial, Helvetica, sans-serif; background-color: #ffffff; border-collapse:collapse;">
                <tbody>
                    <!--Top area-->
                    <tr>
                        <td align="center">
                            <table width="600" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #ffffff; border-collapse: collapse">
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
                                                            <img src="IPMLogo/ipm-logo.png" >
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
                                                                        <td style="font-family:  Arial, Helvetica, sans-serif; font-size: 14px;color: rgba(0,0,0,0.56);text-align: right"><span>Invoice No : </span>INV0002589 - '.str_pad($invoiceId , 4, '0', STR_PAD_LEFT).'</td>
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
                            <table width="640"  cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #25c3cc; border-collapse: collapse">
                                <tbody>
                                    <tr>
                                        <td style="height: 31px"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width="84%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #ffffff; border-collapse: collapse">
                                                <tr>
                                                    <td colspan="3" style="padding: 20px"></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 40px;"></td>
                                                    <td>
                                                        <table width="100%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #ffffff; border-collapse: collapse">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="color:#020202;font-weight: bold;font-size: 14px;font-family:  Arial, Helvetica, sans-serif;padding-bottom: 22px;">Hi '.$username.',</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="color:#333333;font-weight: 400;font-size: 14px;font-family:  Arial, Helvetica, sans-serif;">
                                                                        This is to confirm your payment of Rs '.$UserAmount.'/= for the registration at National HRConference 2016 organized by IPM Sri Lanka <a style="color:rgba(51, 51, 51, 0.89);text-decoration: none; font-weight: 600; " href="mailto:ipminfo@ipmlk.org">(ipminfo@ipmlk.org)</a> .
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
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">'.$count.'</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">'.$chargeperHead.' LKR</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">'.$firstAmount.'.00 LKR</td>
                                                                </tr>
                                                                <tr style="vertical-align: top; color: #333333;">
                                                                    <td style="width: 203px; padding: 10px 0 0 20px; border-right: 1px solid #d5d5d5; text-align: left;">Research Symposium </td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">'.$researchCount.'</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">3000.00 LKR</td>
                                                                    <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; border-bottom: 1px solid #d5d5d5; text-align: right">'.$invoiceResearchAmount.'.00 LKR</td>
                                                                </tr>

                                                                <tr style="vertical-align: top; color: #333333;">
                                                                    <td style="width: 203px; padding: 10px 0 0 20px; border-right: 1px solid #d5d5d5; text-align: left;">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; text-align: right">'.$secondTotal.'.00 LKR</td>
                                                                </tr>
                                                                <tr style="vertical-align: top; color: #333333;">
                                                                    <td style="width: 203px; padding: 10px 0 0 20px; border-right: 1px solid #d5d5d5; text-align: left;">NBT 2%</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; border-bottom: 1px solid #d5d5d5; text-align: right">'.$NBT.'.00 LKR</td>
                                                                </tr>

                                                                <tr style="vertical-align: top; color: #333333;background: #f5f5f5">
                                                                    <td style="width: 203px; padding: 10px 0 0 20px; text-align: left; text-transform: uppercase; font-weight: bold">Total</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 0 0; border-right: 1px solid #d5d5d5; text-align: right">&nbsp;</td>
                                                                    <td style="width: 118px; padding: 10px 20px 10px 0; border-right: 1px solid #d5d5d5; border-bottom: 1px solid #d5d5d5; text-align: right; text-transform: uppercase; font-weight: bold">'.$finalAmount.'.00 LKR</td>
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
                                                                       '.$company_contact_title.$company_contact_name.',
                                                                        <br>Designation:'.$company_contact_designation.',
                                                                        <br>'.$company_contact_email.',
                                                                        <br>'.$company_contact_phone.'.

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
                            <table width="600" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #090924; border-collapse: collapse">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table width="94%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #090924; border-collapse: collapse">
                                                <tbody>
                                                    <tr>
                                                        <td style="height: 22px;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: #ffffff; font-weight: bold; font-size: 24px; text-align: center"><span>Invoice No : </span>INV0002589 - '.str_pad($invoiceId , 4, '0', STR_PAD_LEFT).'</td>
                                                        <td><img src='.$filename.' /></td>
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
                                            <table width="94%" cellspcing="0" border="0" cellpadding="0" style="font-family:  Arial, Helvetica, sans-serif; background: #090924; border-collapse: collapse">
                                                <tbody style="color: #676773; font-weight: 400; font-size: 14px; text-align: left">
                                                    <tr >
                                                        <td style="padding-bottom: 20px;color: #676773;">By using these products, you agree that you are bound by the <a href="#a" style="color: #cdcdcd; text-decoration: none;border-bottom: 2px solid #84848a;">Terms of Service</a> and <a href="#a" style="color: #cdcdcd; text-decoration: none;border-bottom: 2px solid #84848a;"> Privacy Policy.</a> Learn more about our  <a href="#a" style="color: #cdcdcd; text-decoration: none;border-bottom: 2px solid #84848a;"> Refund Policy</a>.</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding-bottom: 20px; color: #676773;">
                                                            Keep your Invoice number (shown above) for future reference. You will need to refer to this number if you need<br>customer service from Institute of Personnel Management Sri Lanka.
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
    ';


        return $this->pdf
            ->load($html)
            ->filename('PDF/'.$invoiceId.'.pdf')
            ->output();



    }






}
