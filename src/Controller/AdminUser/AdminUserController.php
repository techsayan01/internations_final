<?php

namespace App\Controller\AdminUser;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;
use App\Repository\UserRepository;

/**
* This controller handles all the user related operations for the application
* The different operations are as follows : 
* 1. Adding a user if the requesting user is admin [POST] -> addUser(Request)
* 2. View all users if the requesting user is admin [POST] -> allUser(Request)
* 3. View the user creation time if the requesting user is admin [POST]  -> userDetail(Request)
* 4. Delete a user if the requesting user is admin [POST] -> deleteUser(Request)
*/

class AdminUserController extends AbstractController {

    //method to have one place for all the custom messages and codes
    //TODO export this method to a class and access it through that class, code reuseability
    private function getMessage($code = NULL){
        $returnResult = false;
        if($code != NULL || $code != ''){
            $message = [
                "401" => "Parameter mismatch",
                "402" => "Content Not Found",
                "403" => "Ran into exception",
                "200" => "Success",
                "201" => "Created Successfully",
                "202" => "Successfully Updated",
                "203" => "Successfully deleted"
            ];
            $returnResult = $message[$code];
        }
        return $returnResult;
    }

   //Checks if the user is admin (needed to reduce uncessary code from the API endpoint/controllers)

    private function isAdmin($username = NULL){
        $returnResult = false;
        if($username == NULL) return false;
        else{
          $isAdmin = $this->getDoctrine()
                          ->getRepository(User::class)
                          ->findByIsAdmin($username);  
          $returnResult = $isAdmin[0]->getIsDeleted() ? false : true;
        }
        return $returnResult;
    }

    #TODO optimize the code by reusing methods
	   /**
     * @Route("/user/add", methods={"POST"}, name="app_internations_post_user_add")
     */
    public function addUser(Request $request){

    	$request->getPreferredLanguage(['en']);
    	$request->headers->get('host');
	    $request->headers->get('content-type');
    	$payload = [
			     "username" 		=> $request->request->get("uname"),
			     "isAdmin"  		=> $request->request->get("isAdmin"),
			     "adminUserName" => $request->request->get("adminUname")
		  ];
      $setData = [];

      if(!isset($payload["username"]) || !isset($payload["adminUserName"])){
        return new JsonResponse([
                  'success' => true,
                  'code'    => "401",
                  'message' => $this->getMessage("401"),
                  'data'    => $setData
              ]);
      }

      else{
        //Check if admin user
  		  if($this->isAdmin($payload['adminUserName']) && ($payload["username"] != null || $payload["username"] == '' ) ){
          $user   = new User();
          $entityManager = $this->getDoctrine()->getManager();
          
          $user = $this->getDoctrine()
                        ->getRepository(User::class)
                        ->findByUsername($payload['username']);
          if($user->getUsername() != null ){
            $user->setUsername($payload["username"]);
            $user->setIsAdmin((isset($payload["isAdmin"]) && ($payload["isAdmin"] == 1 || $payload["isAdmin"] == "true") ) ? true : false );
            $entityManager->persist($user);
            $entityManager->flush();
          }
          else{
            // die;
            $message = "Already present"; 
          }

          try { 
              return new JsonResponse([
                    'success' => true,
                    'code'    => "200",
                    'message' => !isset($message) ? $this->getMessage("200") : $message,
                    'data'    => $setData
                ]);
            }
            catch(\Exception $exception) {
                
                return new JsonResponse([
                    'success' => false,
                    'code'    => "403",
                    'message' => $exception->getMessage(),
                    'data'    => $setData
                ]);
             }   
          }
        }
      }

    /**
     * @Route("/user/all", methods={"POST"}, name="app_internations_post_user_all")
     */
    public function allUser(Request $request){

    	$request->getPreferredLanguage(['en']);
	    $request->headers->get('content-type');

      $setData = [];
		  $payload = [
            "adminUser" => $request->request->get("adminUname")
      ];
      if($this->isAdmin($payload["adminUser"])){
        $user = new User();
        $userListObject = $this->getDoctrine()
                          ->getRepository(User::class)
                          ->findAll();

          //loop through the userListObject to fetch all the users for the admin
          foreach ($userListObject as $key => $value) {
              if($value->getIsDeleted() == 0)
                  $setData = [
                      "userId"   => $value->getUserId(),
                      "username" => $value->getUsername(),
                      "createAt" => $value->getCreatedAt()
                  ];
          }
          try { 
            return new JsonResponse([
                  'success' => true,
                  'code'    => "200",
                  'message' => $this->getMessage("200"),
                  'data'    => $setData
              ]);
          }
          catch(\Exception $exception) {
              
              return new JsonResponse([
                  'success' => false,
                  'code'    => $exception->getCode(),
                  'message' => $exception->getMessage(),
                  'data'    => $setData
              ]);
          }   
      }
    }

    #TODO write the proper else condition to reduce API errors

    /**
     * @Route("/user/detail", methods={"POST"}, name="app_internations_post_user")
     */
    public function userDetail(Request $request){

    	$request->getPreferredLanguage(['en']);
	    $request->headers->get('content-type');
    	$payload = [
			      'username'     => $request->request->get("username"),
            'adminUser'    => $request->request->get("adminUname") 
		        ];
      $setData = [];

      //check the details of the user if the requested user is admin
      if($this->isAdmin($payload['adminUser'])){
  		$user       = new User();
  		$userObject = $this->getDoctrine()
                        ->getRepository(User::class)
                        ->findByUsername($payload['username']);
      $setData = count($userObject) ? $userObject[0] : false;
            try {
                return new JsonResponse([
                    'success' => true,
                    'code'    => "200",
                    'message' => $this->getMessage("200"),
                    'data'    => $setData
                ]);
            }
            catch(\Exception $exception) {
                
                return new JsonResponse([
                    'success' => false,
                    'code'    => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'data'    => $setData
                ]);
            } 
      }
    }

    #TODO optimize the reusability factor in this method
    /**
     * @Route("/user/delete", methods={"POST"}, name="app_internations_post_user")
     */

    public function deleteUser(Request $request){
      
      $request->getPreferredLanguage(['en']);
      $request->headers->get('content-type');
      $payload = [
             'username'     => $request->request->get("uname"),
             'adminUser'    => $request->request->get("adminUname") 
              ];
      $setData = [];  
      if($this->isAdmin($payload['adminUser'])){
        $user = $this->getDoctrine()
                        ->getRepository(User::class)
                        ->findByUsername($payload['username']);
        if(!is_null($user)){
            $entityManager = $this->getDoctrine()->getManager();
            $user->setIsDeleted(1);
            $entityManager->persist($user);
            $entityManager->flush();
            $setData = [
              "isDeleted" => $user->getUsername()
            ];
        }
        else{
          $setData = [
            "isDeleted" => $payload["username"]
          ];
        }
      }
      else{
        $setData = [
          "isDeleted" => $payload["username"]
        ];
      }

         try {
                return new JsonResponse([
                    'success' => true,
                    'code'    => "200",
                    'message' => $this->getMessage("200"),
                    'data'    => $setData
                ]);
            }
            catch(\Exception $exception) {
                
                return new JsonResponse([
                    'success' => false,
                    'code'    => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'data'    => $setData
                ]);
            }
    }
}
