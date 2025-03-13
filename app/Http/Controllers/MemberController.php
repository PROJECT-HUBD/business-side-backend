<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member; // 確保有引入 Model


class MemberController extends Controller
{
    public function index(){
        return response()->json(Member::all());
    }
}
