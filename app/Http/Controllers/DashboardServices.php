<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\QR\BarcodeQR;
use App\QR\qrlib;
use App\MailClass\PHPMailer;


class DashboardServices extends Controller
{

    public function registrationAndPayments(Request $request){


   	   	$PaymentDetails = DB::select("select (select k.packagers_name from packagers k where k.packagers_id = a.company_packagers) as 'name' ,
			  	
		SUM((select count(p.participant_id) from participant p where p.participant_company = a.company_id ))
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

    	$date = $request->input("agendaDate");

    	$result = DB::select("select * from agenda where  item_date = STR_TO_DATE('$date', '%d/%m/%Y %r')");


    	return response()->json(['data' => $request]);
    }

     public function saveAgenda(Request $request){


     	
     	$name        = $request->input("itemName");
     	$description = $request->input("itemDescription");
     	$time        = $request->input("itemTime");
     	$date        = $request->input("ItemDate");


     	$res1 = DB::statement("insert into agenda(item_name,item_description,item_time,item_date)
			values('$name','$description','$time',STR_TO_DATE('$date', '%d/%m/%Y %r'))
			");


		if($res1){
     		 return response()->json(['message' => "success"]);
     	}else{

     		return response()->json(['message' => "fail"]);
     	}

    }

    public function updateAgenda(Request $request){

    	$id 		 = $request->input("itemId");
    	$name        = $request->input("itemName");
     	$description = $request->input("itemDescription");
     	$time        = $request->input("itemTime");
     	$date        = $request->input("ItemDate");


     	$res1 = DB::statement(DB::raw("UPDATE agenda set item_name = '$name',item_description = '$description',item_time = '$item_time',
     		 item_date = STR_TO_DATE('$date', '%d/%m/%Y %r') where id = '$id'"));

     	if($res1){
     		 return response()->json(['message' => "success"]);
     	}else{

     		return response()->json(['message' => "fail"]);
     	}


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

    	return response()->json(['data' => $result]);
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




}
