<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Schedule;
use App\Employee;
use App\Booking;
use Illuminate\Support\Str;

class MondayController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $seatArray = [1, 5, 9, 13, 17, 21, 25, 29, 33, 37, 41, 2, 6, 10, 14, 18, 22, 26, 30, 34, 38, 42, 43, 3, 7, 11, 15, 19, 23, 27, 31, 35, 39, 44, 4, 8, 12, 16, 20, 24, 28, 32, 36, 40, 45];
        $booking_days = array('Monday','Tuesday','Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

        $monSched = Schedule::where('isClosed', 0)->where('travel_day', 'Monday')->first();
        $monSchedString = new Carbon($monSched->travel_date);
        $satSched = Schedule::where('isClosed', 0)->where('travel_day', 'Saturday')->first();
        $satSchedString = new Carbon($satSched->travel_date);
        $displayMain = $this->displayBooking($booking_days);
        $monFormat = $monSchedString->format('Y-m-d');

        $employee = Booking::where('sched', $monFormat)->get();
        $bookingCount = $employee->count();

        $reservedSeats = $this->getReservedSeats($employee);
        return view('monday', compact('monSched', 'satSchedString', 'monSchedString', 'seatArray', 'displayMain', 'reservedSeats', 'bookingCount'));
    }

    public function getReservedSeats($seats)
    {
        $reservedSeats = [];
        foreach ($seats as $seat) {
            array_push($reservedSeats, $seat->seatNumber);
        }

        return $reservedSeats;
    }

    public function displayBooking($booking_days)
    {

        $today = Carbon::now();
        $dayToday = $today->isoFormat('dddd');
        // $dayToday = 'Wednesday';
        $tym = $today->format('H');

        if (in_array($dayToday, $booking_days)) {
            if ($dayToday == 'Tuesday' && $tym < 7) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function store(Request $request)
    {            
        $monSched = Schedule::where('isClosed', 0)->where('travel_day', 'Monday')->first();
        $monSchedString = new Carbon($monSched->travel_date);
        $monFormat = $monSchedString->format('Y-m-d');
        $bookings = Booking::where('sched', $monSched->travel_date)->get();
        $employee = Employee::where('empId', $request->employee)->first();              
        $bookEmployee = Booking::where('sched', $monSched->travel_date)->where('employee_id', $employee->empId)->first();
        $booking_days = array('Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $todayDay = Carbon::now()->isoFormat('dddd');
        //$todayDay = 'Wednesday';
        $totalMinutes = Carbon::now()->diffInMinutes(Carbon::today());
        // $totalMinutes = 420;
        // dd($totalMinutes);

        if ($bookEmployee) {
            $request->session()->flash('errorMesssage', '<strong>Booking failed!</strong> Your ID number already has a booked seat.');
            return redirect()->back();
        }

        if ($employee->blacklists) {
            $request->session()->flash('errorMesssage', '<strong>Booking failed!</strong> Your account has been blacklisted, Please contact the admin at GSD office local 3113.');
            return redirect()->back();
        }

        if ($employee->isNotActive) {
            $request->session()->flash('errorMesssage', '<strong>Booking failed!</strong> Your ID number does not exist on our record. Please contact GSD office @ local 3113');
            return redirect()->back();
        }

        foreach ($bookings as $booking) {
            if ($booking->seatNumber == $request->sit) {
                $request->session()->flash('errorMesssage', '<strong>Booking failed!</strong> The seat is already taken.');
                return redirect()->back();
            }
        }

        if (!in_array($todayDay, $booking_days)) {
            $request->session()->flash('errorMesssage', '<strong>Booking failed!</strong> You are not yet allowed to book today. Booking schedule for PRIORITY 1 starts on WEDNESDAY, PRIORITY 2 on THURSDAY, and CHANCE PASSENGERS on SATURDAY');
            return  redirect()->back();
        }

        if ($todayDay == 'Wednesday'){
            if($totalMinutes < 420){
                $request->session()->flash('errorMesssage', '<strong>Booking failed!</strong> You are not yet allowed to book this time. Booking starts at Wednesday, 7:00 AM Sharp.');
                return  redirect()->back();
            }
        }
        if ($todayDay == 'Wednesday') {
            if (!$employee->priorities) {
                $request->session()->flash('errorMesssage', '<strong>Booking failed!</strong> You are not yet allowed to book today. Booking schedule for CHANCE PASSENGERS are every SATURDAY');
                return  redirect()->back();
            }else{
                if($employee->ptype() != 1){
                    $request->session()->flash('errorMesssage', '<strong>Booking failed!</strong> You are not yet allowed to book today because you are a PRIORITY '.$employee->ptype().' PASSENGER. Booking schedule for PRIORITY 2 PASSENGERS are every THURSDAY');
                    return  redirect()->back();
                }
            }
        }
        if ($todayDay == 'Thursday' || $todayDay == 'Friday') {
            if (!$employee->priorities) {
                $request->session()->flash('errorMesssage', '<strong>Booking failed!</strong> You are not yet allowed to book today. Booking schedule for CHANCE PASSENGERS are every SATURDAY');
                return  redirect()->back();
            }
        }

        $newBooking = new Booking([
            'seatNumber' => $request->sit,
            'employee_id' => $employee->empId,
            'destination' => $request->destination,
            'pword' => Str::random(4),
            'sched' => $monSched->travel_date
        ]);
        $newBooking->save();

        if ($employee->address1 == null) {
            $employee->address1 = $request->address1;
            $employee->address2 = $request->address2 == 'Others' ?  $request->address2m : $request->address2;
            $employee->save();
        }

        $request->session()->flash('successMesssage', '<strong>Success!</strong> Your booking was confirmed. Your password is <strong style="font-size:20px;color:blue;"">' . $newBooking->pword . '</strong> use this to cancel or verify your booking info.');
        //$request->session()->flash('successMesssage', '<strong>Success!</strong> Your booking was confirmed. Your password is <strong style="font-size:20px;color:blue;"">TEST PWORD HEHE</strong> use this to cancel or verify your booking info.');
        return  redirect()->back();
    }
}
