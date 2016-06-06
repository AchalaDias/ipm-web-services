<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\QR\BarcodeQR;

class DashboardServices extends Controller
{
    
    public function registrationAndPayments(Request $request){


   	   	$PaymentDetails = DB::select("select (select k.packagers_name from packagers k where k.packagers_id = a.				    company_packagers) as 'name' ,
			  	 count(a.company_packagers)
				as 'registered',
			    (select count(*) from company where company_paymant_status != 0  and company_packagers = a.company_packagers)    
       			as 'paid'
				from  company a
				group by a.company_packagers");


   // $jsonArray = json_encode($PaymentDetails);


     return response()->json(['data' =>   $PaymentDetails]);

    }

    public function targetVsAchievment(Request $request){





    }

      public function registrationVsAttendance(Request $request){

    	


    }


    public function CompanyPayment(Request $request){
      	
    	


    }

    public function IndividualPayment(Request $request){
      	
    	


    }

     public function CompanyList(Request $request){
      	
    	$result = DB::select("select * from company a where a.company_com_indiv =0 ");
        

        return response()->json(['data' =>   $result]);

    }

       public function IndividualList(Request $request){
      	
    	$result = DB::select("select * from participant a where a.participant_company in (select company_id from company where company_com_indiv =1 )");
       

        return response()->json(['data' =>   $result]);


    }


    public function PaymentProcess(Request $request){


    	$payment_method = "cash";
    	$cheque_no = "null";
    	$bank = "null";
    	$branch = "null";
    	$amount = 23905;


    	/*$res =   DB::statement(DB::raw("INSERT INTO payments(payment_method,cheque_no,bank,branch,amount) values('$payment_method',$cheque_no,'$bank','$branch',$amount)")); */

    	 $PNG_TEMP_DIR = "C:\/xampp\htdocs\IPMwebServices\/QRpng/";

    	   $filename = $PNG_TEMP_DIR.'test.png';

    	   $errorCorrectionLevel = 'L';
    	   $matrixPointSize = 4;

    	   echo $PNG_TEMP_DIR;



    	   $BarcodeQR = new BarcodeQR();


    	   $BarcodeQR->
    	 



  

    }
}
