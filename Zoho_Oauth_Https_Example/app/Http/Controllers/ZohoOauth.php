<?php

namespace App\Http\Controllers;
use App\Zoho;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;

class ZohoOauth extends Controller
{
    public function __construct()
    {
        $this->scope = env('ZOHO_SCOPES');

        $this->client_id = env('ZOHO_CLIENT_ID');

        $this->redirect_uri = env('ZOHO_REDIRECT_URI');

        $this->client_secret = env('ZOHO_CLIENT_SECRET');
    }

    public function Redirect(){

        $scope = env('ZOHO_SCOPES');
        $client_id = env('ZOHO_CLIENT_ID');
        $redirect_uri = env('ZOHO_REDIRECT_URI');

        $url = "https://accounts.zoho.com/oauth/v2/auth?scope=$scope&client_id=$client_id&response_type=code&access_type=offline&redirect_uri=$redirect_uri";

        return redirect($url);

    }

    pubLic function HandleRedirect(Request $request){
        $client_secret = env('ZOHO_CLIENT_SECRET');
        $scope = env('ZOHO_SCOPES');
        $client_id = env('ZOHO_CLIENT_ID');
        $redirect_uri = env('ZOHO_REDIRECT_URI');


        if($request->error !='access_denied'){
            $code = $request->code;
            $location = $request->location;
            $account_server = $request['accounts-server'];

            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => "$account_server/oauth/v2/token?grant_type=authorization_code&client_id=$client_id&client_secret=$client_secret&redirect_uri=$redirect_uri&code=$code",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST", ));

            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response);
            $access_token = $data->access_token;
            $refresh_token = $data->refresh_token;
            $api_domain = $data->api_domain;
            $expire = $data->expires_in;

            $expire_stamp = date('Y-m-d H:i:s', strtotime("+$expire sec"));

            Zoho::Create(['account_server'=>$account_server,'refresh_token'=>Crypt::encryptString($refresh_token),
            'accces_token'=>Crypt::encryptString($access_token),'api_domain'=>$api_domain, 'expire_time'=>$expire_stamp]);

            echo "User Data Added Successfully";

        }
    }

    public function AccessTokenRefresh($refresh_token, $client_id, $client_secret, $account_server, $lp_id){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "$account_server/oauth/v2/token?refresh_token=$refresh_token&client_id=$client_id&client_secret=$client_secret&grant_type=refresh_token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response);
        $access_token = $data->access_token;
        Zoho::where('id', $lp_id)->update(['access_token' => $access_token]);
        return ($access_token);
    }

    public function CreateLead(){
        $id = 4;
        $user = Zoho::where('id',$id)->first();

        if($user){
            $expire_time = $user['expire_time'];
            $expire = date('Y-m-d H:i:s',strtotime($expire_time));
            $timestamp_now = date('Y-m-d H:i:s');
            if($expire > $timestamp_now){
                $token = Crypt::decryptString($user['accces_token']);
                $api_domain = $user['api_domain'];
                $account_server = $user['account_server'];
                SendLead($api_domain , $token);

            }else
                {
                $refresh_token = Crypt::decryptString($user['refresh_token']);
                $api_domain = $user['api_domain'];
                $client_id = env('ZOHO_CLIENT_ID');

                $redirect_uri = env('ZOHO_REDIRECT_URI');
                $account_server = $user['account_server'];
                $token = AccessTokenRefresh($refresh_token, $client_id, $client_secret, $account_server, $lp_id);

                SendLead($api_domain, $token);

                    }
                }
    }

    public function SendLead($api_domain , $token){
        #Required date_create_from_format
        $data = array (
          'data' =>
          array (
            0 =>
            array (
              'Company' => 'Zylker',
              'Last_Name' => 'Daly',
              'First_Name' => 'Paul',
              'Email' => 'p.daly@zylker.com',
              'State' => 'Texas')));

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "$api_domain/crm/v2/Leads",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>"$data",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Zoho-oauthtoken $token",
            "Content-Type: text/plain",

        ),
        ));

        $response = curl_exec($curl);
        $http_respond = trim(strip_tags($response));
        $http_code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        curl_close($curl);

        if($http_code == "200"){
            echo "Lead Pushed Successfully";
                }
        else{
            echo "Something Went Wrong";
                }
    }
}
