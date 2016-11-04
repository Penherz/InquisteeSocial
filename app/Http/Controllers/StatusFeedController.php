<?php

namespace App\Http\Controllers;
use App\Http\Requests\CreateStatusRequest;
use App\Jobs\CreateStatusJob;
use App\Repositories\Status\StatusCommendRepository;
use Illuminate\Support\Facades\Request;
use App\Models\Status;
use App\Models\Commend;

class StatusFeedController extends Controller
{


/*
|--------------------------------------------------------------------------
| StatusFeedController
|--------------------------------------------------------------------------
|
| Handles all shit related the the feeds related to the web which includes
| getting the feeds(index), storing a new feed(store), obtaining more
| feeds (more), and making commends(commend)
|
*/

    public function index(StatusCommendRepository $statusStuff){
        $statusThighs=$statusStuff->getStatusAndCommendsLeaderUser(Auth::user());
        return view('',['status'=>$statusThighs['Status'],'commends'=>$statusThighs['Commends']]);
        
    }

    public function store(CreateStatusRequest $request){
        $this->dispatch(new CreateStatusJob($request));

        return response()->json(true);
    }

    public function more(Request $request, StatusCommendRepository $statusStuff ){
        $statusThings=$statusStuff->getStatusAndCommendsLeaderUserAjax(Auth::user(),$request->skipQty);
        return response()->json([
            'commend'=>$statusThings['Commends'],
            'status'=>$statusThings['Status'],
        ]);
    }

    public function commend(Request $request,Status $id){
        /*
        |----------------------------------------------------
        | Making a Commend
        |----------------------------------------------------
        | This methods make a commend by taking in id of the
        | Status and making a model of it, Instantiating a
        | the Status Model to have the Commend with the
        | user name and status id.
        |
        |----------------------------------------------------
        | Note: the commend action would fire a notification
        |       event that's going to be responded to by
        |       adding the notification to database.
        |
        */
        $id->commends()->create(
            [
                'commend'=>$request['commend'],
                'user_id'=>Auth::user()->id,
            ]);

        return response()->json(
            ['commend_count'=>count(Commend::where('status_id',$id->id))]
        );


    }
}
