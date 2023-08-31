<?php

namespace App\Http\Controllers\Admin;

use App\Blacklist;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Employee;
use App\Booking;
use App\Services\RoleRightService;
use App\Priority;

class EmployeesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        RoleRightService $roleRightService
    ) {
        // $this->middleware('auth');
        $this->roleRightService = $roleRightService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
       
        $rolesPermissions = $this->roleRightService->hasPermissions("Employee Listing");
        if (!$rolesPermissions['view']) {
            abort(401);
        }

        $create = $rolesPermissions['create'];
        $edit = $rolesPermissions['edit'];
        $search = $rolesPermissions['search'];
        $print = $rolesPermissions['print'];
        $pagination = $rolesPermissions['pagination'];

        $currentDate = Carbon::now();
        $employees = Employee::where(function ($query) use ($currentDate) {
            $query->whereNull('expireDate')
                  ->orWhere('expireDate', '>', $currentDate);
        })->paginate(15);

        return view('admin.employees', compact(
            'employees',
            'create',
            'edit',
            'search',
            'print',
            'pagination'
            
        )); 
    }

    public function destroy($id)
{
        
        $employee = Employee::findOrFail($id);
        $employee->delete();
        
        return redirect()->route('admin.employees')->with('success', 'Employee deleted');
}


    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'expireDate' => 'required',
        ]);

        if ($emp = Employee::latest()->first()) {
            $empId = $emp->cid + 1;
        } else {
            $empId = 1;
        }

        $employee = new Employee([
            'name' => $request->get('name'),
            'expireDate' => $request->get('expireDate'),
            'empId' => $this->gen_empid($empId),
            'cid' => $empId
        ]);

        //$employee->save();
        //return redirect('admin/employees');
              
        $employee->save();
        $request->session()->flash('success', 'Employee has been added successfully!');
        return redirect('admin/employees');        
    }

    public function gen_empid($n)
    {
        $r = strlen($n);
        $e = 4 - $r;
        $z = "";
        for ($x = 1; $x <= $e; $x++) {
            $z .= "0";
        }
        $refcode = $z . $n;
        // return "PMC-C" . $refcode;
        return "PMC-C" . $refcode;
    }

    public function deactivate($id, Request $request)
    {

        $employee = Employee::where('empId', $id)->first();
        if ($request->has('deleteBooking')) {
            $employee->update(['isNotActive' => 1]);
            $employee->bookings()->whereDate('sched', '>=', Carbon::now()->format('Y-m-d'))->delete();
        } else {
            if ($employee->bookings()->whereDate('sched', '>=', Carbon::now()->format('Y-m-d'))->first()) {
                \Session::put('booking', $employee->empId);
            } else {
                $employee->update(['isNotActive' => 1]);
            }
        }

        return redirect('admin/employees');
    }
    public function activate($id, Request $request)
    {
        $employee = Employee::where('empId', $id)->first();
        $employee->update(['isNotActive' => 0]);
        return redirect('admin/employees');
    }

    public function search(Request $request)
    {
        $rolesPermissions = $this->roleRightService->hasPermissions("Employee Listing");
        if (!$rolesPermissions['view']) {
            abort(401);
        }

        $create = $rolesPermissions['create'];
        $edit = $rolesPermissions['edit'];
        $search = $rolesPermissions['search'];
        $print = $rolesPermissions['print'];
        $pagination = $rolesPermissions['pagination'];

        $q = $request->get('q');

        // $employees = Employee::where('name', 'LIKE', '%' . $q . '%')
        //     ->orWhere('empId', 'LIKE', '%' . $q . '%')
        //     ->select('*')
        //     ->paginate(15)->setPath('');

            $currentDate = Carbon::now();
            $employees = Employee::withTrashed()->where(function ($query) use ($currentDate) {
                $query->whereNull('expireDate')
                      ->orWhere('expireDate', '>', $currentDate);
            })
            ->where(function ($query) use ($q){
                $query->where('name', 'LIKE', '%' . $q . '%')
                      ->orWhere('empId', 'LIKE', '%' . $q . '%');

            })->paginate(15);


        $employees->appends(array(
            'q' => $request->get('q')
        ));

        return view('admin.employees', compact(
            'employees',
            'create',
            'edit',
            'search',
            'print',
            'pagination'
        ));
    }

    public function priorities($id, Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $priority = new Priority([
            'employee_id' =>    $request->get('id'),
            'priority_type' =>  $request->get('ptype'),
        ]);

        $priority->save();

        return redirect('admin/employees');
    }

    public function destroy_priorities($id)
    {

        $priority = Priority::findOrFail($id);
        $priority->delete();

        return redirect('admin/employees');
    }

    public function blacklists($id, Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $blacklist = new Blacklist([
            'employee_id' => $request->get('id'),
        ]);

        $blacklist->save();

        return redirect('admin/employees');
    }

    public function destroy_blacklists($id)
    {

        $blacklist = Blacklist::findOrFail($id);
        $blacklist->delete();

        return redirect('admin/employees');
    }
}
