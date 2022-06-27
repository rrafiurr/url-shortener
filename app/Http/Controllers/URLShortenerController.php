<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\url;
use Illuminate\Support\Facades\Http;

class URLShortenerController extends Controller
{
    /**
     * shortener a new url.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function shortener(Request $request)
    {
        $url = strpos($request->url, 'http') !== 0 ? "http://$request->url" : $request->url;

        // echo filter_var($url, FILTER_VALIDATE_URL); die;

        $rules = array(
            "url" => ['required']
        );
        
        $validator = Validator::make($request->all(), $rules);

        $response = [];
        if($validator->fails())
        {
            $response['error'] =  $validator->errors();
        }
        elseif(!$this->validateURL($url))
        {
            $response['error'] =  "This url is not valid";
        }
        else{
            
            
            $urlExist = url::where('url',$url)->first();
            
            if($urlExist)
            {
                $response ['exist'] = 1;
                $response ['url'] = env('APP_URL')."/".$urlExist->hash;
            }
            else{
                $r = $this->checkSafeBrowsing($url); // checking the url is validate by google safe browsing
                if(!get_object_vars($r))   // check the reponse is empty or not. If the reponse is empty then this code will go through
                {

                    $hash = $this->getUniqueHash();
                    //save into database
                    $url = Url::firstOrCreate(
                        [ 'url' => $url ],
                        [ 'hash' => $hash ],
                    );
                    
                    $response ['exist'] = 0;
                    $response ['url'] = env('APP_URL')."/".$hash;
                }
                else{
                    $response['error'] =  'The url is not safe';
                }
            }

            
        }

        return response()->json($response);
    }

    /**
     * validate an url.
     *
     * @param  $url
     * @return boolean
     */
    private function validateURL($URL) 
    {
        return preg_match('%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu', $URL);
    }

    /**
     * MAke a unique 6 alphanumaric hash.
     *
     * @param  
     * @return 6 digit unique hash
     */
    private function getUniqueHash()
    {
        $hash = "";
        
        
        while($hash == "" || url::where('hash',$hash)->exists())
        {
            $hash = substr(md5(uniqid(rand(), true)), 0, 6);
        }

        return $hash;
    }
    
    /**
     * Redirect the matched url.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return ridrect to the matched URL
     */
    public function redirection(Request $request)
    {
        $hash = $request->hash;

        if($hash != "")
        {  
            $url = Url::where('hash',$hash)->first();
            if($url)
            {
                return redirect($url->url);    
            }
        }

        //the hash that did not found on the database and also the validation fails redirect to home page
        return redirect('/');
    }

    /**
     * Api call to google safe browsing 
     *
     * @param  $url
     * @return API Response
     */
    private function checkSafeBrowsing($url) {

        $body = [
            'threatInfo' => [
                'threatTypes' => [
                    'MALWARE', 'SOCIAL_ENGINEERING'
                ],
                'platformTypes' => [ 
                    'WINDOWS' 
                ],
                'threatEntryTypes' => [
                    'url'
                ],
                'threatEntries' => [
                    'url' => $url
                ]
            ]
        ];  
        // return $body;
        $response = Http::accept('application/json')
            ->withBody(json_encode($body), 'application/json')
            ->post('https://safebrowsing.googleapis.com/v4/threatMatches:find?key='. env('GOOGLE_SAFE_BROWSING_KEY'));

        return json_decode($response);
    }   
}
