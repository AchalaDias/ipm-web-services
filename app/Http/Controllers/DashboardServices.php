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

    	$res1 = DB::statement(DB::raw("INSERT INTO payments(participant_id,payment_method,payment_role,cheque_no,bank,branch,amount) values('$id','$payment_method','company',$cheque_no,'$bank','$branch',$IndividualAmonut)"));

		}

    	$res2 = DB::statement(DB::raw("UPDATE company SET company_payment_type = '$payment_method',company_apyment_amount = '$amount',company_payment_status = 1 where participant_company = '$company_id'"));

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


    	$res1 =   DB::statement(DB::raw("INSERT INTO payments(participant_id,payment_method,payment_role,cheque_no,bank,branch,amount) values('$participant_id','$payment_method','indv',$cheque_no,'$bank','$branch',$amount)"));


    	$res2 =   DB::statement(DB::raw("UPDATE company SET company_payment_type = '$payment_method',company_apyment_amount = '$amount',company_payment_status = 1 where participant_company = '$company_id'"));


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
      (select count(*) from  participant b where b.participant_company = a.company_id) as user_count
       from company a where a.company_com_indiv =0 ");


        return $result;

    }
 
     public function IndividualList(Request $request){
 

     $individual = DB::select("select a.* ,
     	(select  b.packagers_name from packagers b where b.packagers_id = a.company_packagers) as packagers_name
     	from participant a where a.participant_company in (select company_id from company where company_com_indiv =1 )");

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

     public function saveAgenda(Request $request){


     	


    }

     public function addSpeakers(Request $request){

     	$name        = $request->input("speakerId");
     	$description = $request->input("speakerDescription");
     	$image_path  = $request->input("speakerImage");
     	$country     = $request->input("speakerCountry");


     	$res1 = DB::statement(DB::raw("INSERT INTO speakers(speaker_name,speaker_description,speaker_datetime,speaker_imgae,speaker_country) values('$name','$description',now(),$image_path,'$image_path','$country')"));


     	if($res1){
     		 return response()->json(['message' => "success"]);
     	}else{

     		return response()->json(['message' => "fail"]);
     	}


    }


    public function ratingAmount(Request $request){

    	$result = DB::select("select a.*,sum(a.rate_amonut),count(a.participant_id) from speakerRating a group by a.speaker_id");

    	return response()->json(['data' => $result]);


    }


 

}
