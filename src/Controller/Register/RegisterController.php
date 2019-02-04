<?php

namespace App\Controller\Register;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
// include_once(__DIR__."A");

class RegisterController
{

	// public function __constructor
    /**
     * @Route("/register/{max}", name="app_internations_get_test")
     */
    public function test($max)
    {
        $number = random_int(0, $max);

        return new Response(json_encode($number));
    }

    /**
     * @Route("/postTest1", methods={"POST"}, name="app_internations_post_test")
     */

    public function postTest(Request $request){

    	$request->getPreferredLanguage(['en']);
    	$request->headers->get('host');
	    $request->headers->get('content-type');
    	$var = $request->request->get('test');
    	
    	try {

    		return new JsonResponse([
         		'success' => true,
            	'data'    => $var // Your data here
        	]);
    	}
    	catch(\Exception $exception) {
    		
    		return new JsonResponse([
            	'success' => false,
            	'code'    => $exception->getCode(),
            	'message' => $exception->getMessage(),
        	]);
    	}
    }
	
	/**
     * @Route("/testtt", methods={"GET"}, name="app_internations_post_test")
     */

	// public function addUser(Request $request, APIUtils $utils){
	// 	// $payload = [
	// 	// 	'username' => $request->request->get('uname'),
	// 	// 	'password' => $request->request->get('pass'),
	// 	// 	'isAdmin'  => $request->request->get('isAdmin')
	// 	// ];
	// 	echo "$utils->sanitize()";
	// }

	// public function isNullable($data, $rejectList = []){

	// 	foreach($data as $key => $value){
			
	// 	}
	// }

}
