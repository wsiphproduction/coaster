<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Employee;
use App\Services\RoleRightService;


class SynchronizeHRISController extends Controller
{   
    public function __construct(
        RoleRightService $roleRightService
    ) {
        $this->roleRightService = $roleRightService;
    }
    public function synchronize_davao()
    {        
        $rolesPermissions = $this->roleRightService->hasPermissions("Sync Employees");
        if (!$rolesPermissions['view']) {
            abort(401);
        }

        $connectionInfodavao = array("Database"=>"PMC-DAVAO", "UID"=>"sa", "PWD"=>"@Temp123!");   
        $davao = sqlsrv_connect("172.16.10.42\philsaga_db", $connectionInfodavao);

        $dq=sqlsrv_query($davao,"select * from viewHREmpMaster where active=1");

        while($d=sqlsrv_fetch_array($dq)){

            $q=sqlsrv_query($davao,"select * from HRDepartment WHERE DeptID = '".$d['DeptID']."' ");

            while($r=sqlsrv_fetch_array($q)){

                $ppp = Employee::where('empId', $d['EmpID'])->first();

                if($ppp){

                    $ppp->update([
                        'empId'     => $d['EmpID'] ,
                        'name'      => mb_convert_encoding( $d['FullName'] , 'UTF-8', 'ISO-8859-1') ,
                        'dept'      => $r['DeptDesc'],
                    ]);     

                } else {

                    if($d['EmpID']) {
                        Employee::create([
                            'empId'     => $d['EmpID'] ,
                            'name'      => mb_convert_encoding( $d['FullName'], 'UTF-8', 'ISO-8859-1'),
                            'dept'      => $r['DeptDesc'],
                            'location'  => 'davao'
                        ]);
                    } else {
                        \Log::info(json_encode($q));
                    }

                }

            }
        }

        return back();
    }


    public function synchronize_agusan() {

        $rolesPermissions = $this->roleRightService->hasPermissions("Sync Employees");
        if (!$rolesPermissions['view']) {
            abort(401);
        }

        $connectionInfoagusan = array("Database"=>"PMC-AGUSAN-NEW", "UID"=>"sa", "PWD"=>"@Temp123!");   
        $agusan = sqlsrv_connect("172.16.20.42\agusan_db", $connectionInfoagusan);

        $dq=sqlsrv_query($agusan,"select * from viewHREmpMaster where active=1");

        while($d=sqlsrv_fetch_array($dq)){

            $q=sqlsrv_query($agusan,"select * from HRDepartment WHERE DeptID = '".$d['DeptID']."' ");

            while($r=sqlsrv_fetch_array($q)){

                $ppp = Employee::where('empId', $d['EmpID'])->first();

                if($ppp){

                    $ppp->update([
                        'empId'     => $d['EmpID'] ,
                        'name'      => mb_convert_encoding( $d['FullName'] , 'UTF-8', 'ISO-8859-1') ,
                        'dept'      => $r['DeptDesc'],
                    ]);     

                } else {

                    if($d['EmpID']) {
                        Employee::create([
                            'empId'     => $d['EmpID'] ,
                            'name'      => mb_convert_encoding( $d['FullName'], 'UTF-8', 'ISO-8859-1'),
                            'dept'      => $r['DeptDesc'],
                            'location'  => 'agusan'
                        ]);
                    } else {
                        \Log::info(json_encode($q));
                    }

                }

            }
        }

        return back();
    }

}