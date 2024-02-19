<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Address;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function createAddress(Request $request)
    {
        $request->validate([
            'province' => 'required',
            'province_id' => 'required',
            'city' => 'required',
            'city_id' => 'required',
            'postal_code' => 'required',
            'address_detail' => 'required',
            'phone_number' => 'required',
            'receiver' => 'required',
            'label' => 'required',
        ]);

        $user = $request->user();

        $addressAmount = $user->address->count();

        $validator = Validator::make(['address_amount' => $addressAmount], [
            'address_amount' => 'lt:3',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'You have reached the maximum amount of addresses']);
        }

        $address = $this->address->create([
            'user_id' => $user->id,
            'province' => $request->province,
            'province_id' => $request->province_id,
            'city' => $request->city,
            'city_id' => $request->city_id,
            'postal_code' => $request->postal_code,
            'address_detail' => $request->address_detail,
            'phone_number' => $request->phone_number,
            'receiver' => $request->receiver,
            'label' => $request->label,
        ]);

        return response()->json([
            'message' => 'Address has been created',
            'address' => $address,
        ]);
    }

    public function updateAddress(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:addresses,id'
        ]);


        $userid = $request->user()->id;
        $address = $this->address->where('id', $request->id)->first();
        if ($request->is_primary) {
            $request->user()->address()->update(['is_primary' => false]);
        }
        if ($address->user_id !== $userid) {
            return response()->json(['error' => 'You are not authorized to update this address']);
        }

        $address->update($request->all());

        return response()->json([
            'message' => 'Address has been updated',
            'address' => $address,
        ]);
    }

    public function deleteAddress(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:addresses,id'
        ]);

        $address = $request->user()->address->find($request->id);

        if ($address->user_id !== $request->user()->id) {
            return response()->json(['error' => 'You are not authorized to delete this address']);
        }

        $address->delete();

        return response()->json([
            'message' => 'Address has been deleted',
        ]);
    }
}
