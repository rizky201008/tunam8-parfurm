<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    protected $address;
    protected $user;
    protected $provinces;

    public function __construct()
    {
        $this->address = new Address();
        $this->user = new User();
        $this->provinces = new Province();
    }

    public function getAddresses(Request $request)
    {
        $addresses = $request->user()->address()->get();
        return response()->json($addresses);
    }

    public function getProvinces()
    {
        $provinces = $this->provinces->all();
        return response()->json($provinces);
    }

    public function getCities($provinceId)
    {
        $apiKey = env('RAJAONGKIR_API');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.rajaongkir.com/starter/city?province=$provinceId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: $apiKey"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json(['error' => $err]);
        } else {
            $reqponse = json_decode($response);
            return response()->json($reqponse->rajaongkir->results);
        }
    }
}
