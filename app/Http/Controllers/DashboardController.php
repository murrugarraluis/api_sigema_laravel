<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use App\Models\Machine;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
	public function index()
	{
//		$collection = collect(
//			[
//				"machines"=>Machine::all(),
//			]
//		);


//		$data = ;
//		dd($collection);
		$collection = [];
		return new DashboardResource($collection);
	}
}
