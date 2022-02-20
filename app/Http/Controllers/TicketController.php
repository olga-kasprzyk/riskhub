<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
           'subject' => 'required|max:255',
           'content' => 'required',
           'author' => 'required|max:255',
           'email' => 'required|max:255',
           'status' => ''
        ]);
        Ticket::create($validator->validated());
    }

    public function update(Ticket $ticket, Request $request){
        $validator = Validator::make($request->all(), [
           'status' => 'required|boolean'
        ]);

        $ticket->update($validator->validated());
    }

    public function index(Request $request, $status = false){
        $query = DB::table('tickets');

        //filter by status if present
        if($status){
            $b = ($status == 'open') ? 0 : 1;
            $query->where('status', '=', $b);
        }

        return $query->paginate(10);
    }

    public function getTicketsByEmail($email){
        $query = DB::table('tickets')->where('email', '=', $email);
        return $query->paginate(10);
    }

    public function getStats(){
        $total = DB::table('tickets')->count();
        $unprocessed = DB::table('tickets')->where('status', '!=', true)->count();
        $last_processed = DB::table('tickets')->where('status', '=', true)->orderBy('updated_at', 'desc')->first();

        $lpv = (isset($last_processed->id)) ? $last_processed->updated_at : 'Never';

        $author = DB::selectOne(DB::raw('select author,  count(1) as `total` from tickets
group by author
order by count(1) desc'));

        return json_encode(['total' => $total, 'unprocessed' => $unprocessed, 'last_processed' => $lpv, 'author' => $author->author]);
    }
}
