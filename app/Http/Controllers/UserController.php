<?php

namespace App\Http\Controllers;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function viewProfile(){
     
        $users =User::all();
        if ($users->count()>0){
            return  UserResource::collection($users->load('blog'));

            return response()->json([
                'status' => 204,
                'message' => 'No users found'
            ], 204); 
        
        }
    }




    public function editProfile(Request $request,User $user){
        $validator =Validator::make($request->all(),[
        'name'=>'required|string',
         'email'=>'required|email',
          'password'=>'required',
          ]);
  
        if ($validator->fails()){
             return response()->json([
              'status'=>400,
               'errors'=>$validator->messages(),
               ],400);
              }
              
               if ($user->id != Auth::id()){
               return response()->json([
               'message' => 'user is not authorized'
                 ], 403);
              
                  }else{
                   $user->name=$request->name;
                   $user->email=$request->email;
                   $user->password=$request->password;
                   $user->save();
                   return response()->json([
                         'status'=>200,
                          'message'=> "user edit successfully"
                       ],200);
                       
                      }
                  }




    public function creatUser(Request $request){
          
                    $validator = Validator::make($request->all(),[
                        'name'=>'required|string',
                        'email'=>'required|email',
                        'password'=>'required',
                    ]);
                     if($validator->fails()){
                         return response()->json([
                          'status'=>422,
                          'errors'=>$validator->messages()
                         ],422);
                         
                     
                     
                     }
                    else{
                             $user= User::create([
                                 'name'=>$request->name,
                                 'email'=>$request->email,
                                 'password'=>$request->password,
                             ]);
                             if($user){
                                 return response()->json([
                                    'status'=>200,
                              'message'=> "user created successfully"
                                 ],200);
                                  
                                 
                     } else{
             
                             return response()->json([
                                 'status'=>500,
                           'message'=> "somthing worng"
                              ],500);
                         }
                     }
                 }    
                 
                
    public function login(Request $request) {


                    $credentials = $request->validate([
                        'email' => 'required|email',
                        'password' => 'required',
                    ]);
                   
                    if (Auth::attempt($credentials)) {
                        $user = Auth::user();
                        $token = $user->createToken('auth_token')->plainTextToken;
             
                        return response()->json([
                            'message' => 'Login successful',
                            'token' => $token,
                            'user' => $user,
                        ], 200);
                     } else {
                        return response()->json([
                            'message' => 'Invalid login credentials'
                         ], 401);
     }
 }               



 public function ShowProfile(){
                    $user =auth()->user();
                    if($user->id != Auth::id()){
                      return response()->json([
                          'message' => 'user is not authorized'
                       ], 403);
                      }
                      return  new UserResource($user->load('blog'));
                   }
              
              
                                   
           
}
