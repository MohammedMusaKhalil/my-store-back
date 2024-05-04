<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function show(Request $request){
        if(!$request->user()->role == "admin"){
            return  response() -> json([
                "message"=> "the user must be admin"],403);
        }
       try{

           $uses = User::all();
         return
         response()->json([
            'status'=>'success',
            'data'=>$uses,
            ],200);
        }catch(\Exception $e){
             return  response() -> json([
                 "status"=>"error",
                 "message"=> $e->getMessage()],$e->getCode());
        }


    }
    public function destroy($id)
    {
        
        try {
            $user = User::find($id);
            if(!$user){
                response()->json(['error' => 'the user not found'], 400);
            }
            $user->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception  $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function edite(Request $request, $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // تحديث البيانات الجديدة للمستخدم
            $user->update($request->all());

            return response()->json(['message' => 'User updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update user'], 500);
        }
    }

}
