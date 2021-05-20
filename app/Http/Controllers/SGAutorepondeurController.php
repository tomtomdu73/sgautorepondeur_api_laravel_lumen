<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SGAutorepondeurController extends Controller
{
    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'nom' => 'string',
            'prenom' => 'required|string',
            'email' => 'required|email',
        ]);

        $data = array(
            'email' => $request->input('email'),
            'attributes' => ["PRENOM" => $request->input('prenom'), "NOM" => $request->has ('nom') ? $request->input('nom') : ''],
            'listIds' => [4]
        );

        try{
            $req = $this->call($data);

            return response()->json(['response' => $req, 'message' => 'success' ], 200);
            
        } catch (Exception $e){

            return response()->json(['response' => 'Error', 'message' => $e], 401);
        }
    }

    public function call($_datas)
    {
        $client = new \GuzzleHttp\Client();
        $endpoint = 'https://api.sendinblue.com/v3/contacts';

        $response = $client->request('POST', $endpoint,[
            'headers' => [
                'api-key' => env('SENDINBLUE_API_KEY')
            ],
            'json' => $_datas
        ]);
        
        if (!$response) {
            throw new Exception('Aucun résultat renvoyé par SendinBlue');
        }

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }
}
