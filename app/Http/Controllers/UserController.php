<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Hash;
use Symfony\Contracts\Service\Attribute\Required;
use Illuminate\Http\Client\Response as ClientResponse;
use Symfony\Component\HttpFoundation\Test\Constraint\ResponseFormatSame;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
    $this->middleware('auth:api');
    }
    public function index()
    {

        try{
            $data = User::all()->toArray();

            return ApiFormatter::sendResponse(200, 'success', $data);
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $this->validate($request, [
             'username'=>'required',
             'email'=>'required',
             'password'=>'required',
             'role'=>'required',
            ]);
            $createuser=User::create([
                'username'=>$request->username,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
                'role'=>$request->role,
            ]);
            return ApiFormatter::sendResponse(200,'berhasil',$createuser);
        }catch(\Exception $e){
            return ApiFormatter::sendResponse(400,$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $data = User::where('id', $id)->first();

            if ( is_null($data)){
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found!');
            }else{
                return ApiFormatter:: sendResponse(200, 'success', $data);
            }
        }catch(\Exception $err) {
            return ApiFormatter:: sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            $getUser = User::find($id);

            if (!$getUser) {
                return ApiFormatter::sendResponse(404, false, 'Data User Not Found');
            } else {
                $this->validate($request, [
                    'username' => 'required',
                    'email' => 'required',
                    'password' => 'required',
                    'role' => 'required'
                ]);

                if($request->password){
                $updateUser = $getUser->update([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                ]);
            }else{
                $updateUser = $getUser->update([
                    'username' => $request->username,
                    'email' => $request->email,
                    'role' => $request->role,
                ]);
            }
                if ($updateUser) {
                    return ApiFormatter::sendResponse(200, true, 'Successfully Update A User Data', $getUser);
                }
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $getUser = User::find($id);

            if (!$getUser) {
                return ApiFormatter::sendResponse(404, false, 'Data User Not Found');
            } else {
                $deleteUser = $getUser->delete();

                if ($deleteUser) {
                    return ApiFormatter::sendResponse(200, true, 'Successfully Delete A User Data');
                }
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function recycleBin()
    {
        try {

            $userDeleted = User::onlyTrashed()->get();

            if (!$userDeleted) {
                return ApiFormatter::sendResponse(404, false, 'Deletd Data User Doesnt Exists');
            } else {
                return ApiFormatter::sendResponse(200, true, 'Successfully Get Delete All User Data', $userDeleted);
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    }
    public function restore($id)
    {
        try {
            $getUser = User::onlyTrashed()->where('id', $id);

            if (!$getUser) {
                return ApiFormatter::sendResponse(404, false, 'Data User Not Found');
            } else {
                $restoreUser = User::onlyTrashed()->where('id', $id)->restore();
                // where => mencari berdasarkan kolom spesifik yang ingin dicari
                // find => mencari berdasarkan kolom primary key
                if ($restoreUser) {
                    $getRestore = User::find($id);

                    return ApiFormatter::sendResponse(200, true, 'Successfully Restore A Deleted User Data', $getRestore);
                }
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    }

    public function forceDestroy($id)
    {
        try {

            $getUser = User::onlyTrashed()->where('id', $id);

            if (!$getUser) {
                return ApiFormatter::sendResponse(404, false, 'Data User for Permanent Delete Doesnt Exists');
            } else {
                $forceUser = $getUser->forceDelete();

                if ($forceUser) {
                    return ApiFormatter::sendResponse(200, true, 'Successfully Permanent Delete A User Data');
                }
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
}
}


    public function login(Request $request){

        try{
        $this->validate($request,[
            'email' => 'required',
            'password'=>'required|min:8',
        ], [
            'email.required'=> 'Email harus di isi',
            'password.required'=>'Password harus di isi',
            'password.min'=> 'Password minimal 8 karakter'
        ]);
        $user = User::where('email',$request->email)->first();

        if(!$user){
            return ApiFormatter::sendResponse(400, false, 'Login Failed!User Doesnt Exists');
        }else{
            $isValid = Hash::check($request->password,$user->password);

            if(!$isValid){
                return ApiFormatter::sendResponse(400, false, 'Login Failed!Password Doesnt Macch');
            }else{
                $generateToken = bin2hex(random_bytes(40));

                $user->update([
                    'token'=>$generateToken
                ]);
                return ApiFormatter::sendResponse(200,'Login successfull',$user);
            }
        }
    }catch(\Exception $e){
        return ApiFormatter::sendResponse(400,false,$e->getMessage());
      }
    }

      public function logout(Request $request)
      {
          try {
              $this->validate($request, ['email' => 'required',
          ]);

          $user = User::where('email', $request->email)->first();

          if (!$user) {
              return ApiFormatter::sendResponse(400, 'login failed! user Doesnt Exist');
          } else {
              if (!$user->token) {
                  return ApiFormatter::sendResponse(400, 'logout failed! user doesnt login Scine');
              } else {
                  $logout = $user->update(['token' => null]);

                  if ($logout) {
                      return ApiFormatter::sendResponse(200, 'logout Successfully');
                  }
              }
          }
      }catch (\Exception $e) {
          return ApiFormatter::sendResponse(400, false, $e->getMessage());
      }
  }
}



