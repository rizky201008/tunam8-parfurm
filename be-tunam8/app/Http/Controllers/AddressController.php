<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Address;
use App\Models\Province;
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
        $client = new Client();

        try {
            $response = $client->request('GET', "https://api.rajaongkir.com/starter/city?province=$provinceId", [
                'headers' => [
                    'key' => $apiKey,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents());

            return response()->json($data->rajaongkir->results);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
