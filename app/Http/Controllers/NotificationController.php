<?php

namespace App\Http\Controllers;

use App\Http\Resources\MyNotificationsResources;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return AnonymousResourceCollection
	 */
	public function index(): AnonymousResourceCollection
	{

		$notifications = Auth()->user()->notifications->where('pivot.send', true)->sortByDesc('date_send_notification')->values();
		return MyNotificationsResources::collection($notifications);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	public function check()
	{
		$notifications = Auth()->user()->notifications->where('pivot.send', true)->where('pivot.is_view', false)->sortByDesc('created_at')->values();
		$notifications->map(function ($notification) {
			$notification->users()->updateExistingPivot(Auth()->user()->id, ['is_view' => true]);
		});
		$notifications = Auth()->user()->notifications->where('pivot.send', true)->sortByDesc('date_send_notification')->values();
		return MyNotificationsResources::collection($notifications);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\Notification $notification
	 * @return \Illuminate\Http\Response
	 */
	public function show(Notification $notification)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \App\Models\Notification $notification
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Notification $notification)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\Notification $notification
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Notification $notification)
	{
		//
	}
}
