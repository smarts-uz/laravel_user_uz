<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Task;
use App\Models\How_work_it;
use Illuminate\Support\Facades\Session;
use Hash;
use TCG\Voyager\Models\Category;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.signin');
    }

    public function createSignin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $categories =Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->get();
        $tasks  =  Task::withTranslations(['ru', 'uz'])->orderBy('id', 'desc')->take(15)->get();
        $howitworks = How_work_it::all();
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = User::find(Auth::user()->id)
            ->update([
                'active_status'=>1,
            ]);
            $lang = Session::pull('lang');
            Session::put('lang', $lang);
            return view('home',compact('tasks','howitworks','categories'))->withSuccess('Logged-in');

        }else {
          return view('auth.signin')->withSuccess('Credentials are wrong.');
        }
      }
    public function signup()
    {
        return view('auth.signup');
    }


    public function customSignup(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $data = $request->all();
        $check = $this->createUser($data);
        return redirect("dashboard")->withSuccess('Successfully logged-in!');
    }


    public function createUser(array $data)
    {
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
    }


    public function dashboardView()
    {
        if(Auth::check()){
            $categories =Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->get();
            $tasks  =  Task::withTranslations(['ru', 'uz'])->orderBy('id', 'desc')->take(15)->get();
            $howitworks = How_work_it::all();
            $lang = Session::pull('lang');
            Session::put('lang', $lang);
            return view('home',compact('tasks','howitworks', 'categories'));
        }
        return redirect("login")->withSuccess('Access is not permitted');
    }


    public function logout() {
          $user = User::find(Auth::id())
          ->update([
              'active_status'=>0,
          ]);
        $lang = Session::pull('lang');
        Session::flush();
        Auth::logout();
        Session::put('lang', $lang);
        return redirect('/');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
