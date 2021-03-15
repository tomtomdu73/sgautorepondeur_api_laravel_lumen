<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SGAutorepondeurController extends Controller
{
    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email',
            'listeid' => 'required|numeric'
        ]);

        $data = $request->all();

        try{
            $req = $this->call('set_subscriber', $data);

            return response()->json(['response' => $req, 'message' => 'success' ], 200);
            
        } catch (Exception $e){

            return response()->json(['response' => 'Error', 'message' => $e], 401);
        }


        
    }

    public function call($action, $_datas)
    {
        $_datas['action'] = $action;
        $_datas['codeactivation'] = env('SGAUTOREPONDEUR_API_KEY');
        $_datas['membreid'] = env('SGAUTOREPONDEUR_MEMBER_ID');

        $handle = curl_init(env('SGAUTOREPONDEUR_API_URL'));
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($_datas));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $req = curl_exec($handle);
        curl_close($handle);

        if ($req === FALSE) {
            throw new Exception('Aucun résultat renvoyé par SG-Autorépondeur');
        }
        return $req;
    }
}
