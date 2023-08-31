<?php

namespace App\Http\Controllers\Admin;

use App\Blacklist;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Role;
use App\Booking;

class RolesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $roles = Role::paginate(15);
        return view('admin.roles', compact('roles'));
    }

    public function store(Request $request)
    {

         $request->validate([
             'role' => 'required',
             'description' => 'required',
         ]);

         $status = $request->has('active');

         $role = new Role([
             'name' => $request->get('role'),
             'description' => $request->get('description'),
             'active' => $status
         ]);

         $role->save();

         return redirect('admin/roles');
    }

    public function deactivate($id, Request $request)
    {
        $role = Role::where('id' ,$id)->first();

         if( $request->has('deleteBooking') ) {

             $role->update(['isNotActive' => 1]);
             $role->bookings()->whereDate('sched', '>=', Carbon::now()->format('Y-m-d'))->delete();

        } else {

             if ($role->bookings()->whereDate('sched', '>=', Carbon::now()->format('Y-m-d') )->first() ) {

                 \Session::put('booking', $role->id);

             } else {

                 $role->update(['isNotActive' => 1]);

             }

         }

         return back();
    }

    public function search(Request $request)
    {
         $q = $request->get('q');

         $roles = Role::where('name', 'LIKE', '%' . $q . '%')
             ->orWhere('id', 'LIKE', '%' . $q . '%')
             ->select('*')
             ->paginate(15)->setPath('');

         $roles->appends(array(
             'q' => $request->get('q')
         ));

         return view('admin.roles', compact('roles'));
    }
}
