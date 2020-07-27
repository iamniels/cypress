<?php

namespace Laracasts\Cypress\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class CypressController
{
    public function login(Request $request)
    {
        $user = factory($this->userClassName())
            ->create($request->all());

        auth()->login($user);

        return $user;
    }

    public function logout()
    {
        auth()->logout();
    }

    public function factory(Request $request)
    {
        return factory($request->input('model'))
            ->times($request->input('times'))
            ->create($request->input('attributes'));
    }

    public function artisan(Request $request)
    {
        Artisan::call($request->input('command'), $request->input('parameters', []));
    }

    public function runPhp(Request $request)
    {
        $code = $request->input('command');

        if ($code[-1] !== ';') {
            $code .= ';';
        }

        if (! Str::contains($code, 'return')) {
            $code = 'return '.$code;
        }

        return response()->json([
            'result' => eval($code),
        ]);
    }

    protected function userClassName()
    {
        return config('auth.providers.users.model');
    }
}
