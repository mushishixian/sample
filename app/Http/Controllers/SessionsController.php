<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionsController extends Controller
{
    //
    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);
        if (\Auth::attempt($credentials, $request->has('remember'))) {
            session()->flash('success', '登录成功!');

            return redirect()->route('users.show', [\Auth::user()]);
        }
        session()->flash('danger', '验证信息不对!');

        return redirect()->back();
    }

    public function destroy()
    {
        \Auth::logout();
        session()->flash('success', '已退出');

        return redirect()->route('login');
    }
}
