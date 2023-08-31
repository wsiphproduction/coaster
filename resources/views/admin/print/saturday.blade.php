<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>PMC - Coaster Booking System</title>

</head>
<body onload="window.print();">
<table width="100%">
	<tr>
		<td><font color="blue" size="+1">PHILSAGA MINING CORPORATION</font><br>
Purok 1-A, Bayugan 3, Rosario, Agusan del Sur<br><br></td>
		<td align="right" style="font-size:11px;" valign="top">{{ $today }}</td>
	</tr>
</table>


     <table width="100%" style="font-family:Arial;font-size:12px;">
	  <tr>
	       <td colspan="6" align="center" style="font-size:14px;font-weight:bold;">SHUTTLE BUS MANIFEST <br>Date Prepared: {{ $today }}</td>
	  </tr>
	  <tr><td>&nbsp;</td></tr>
    <tr>
      <td colspan="6">
        <table width="100%" style="font-size:14px;">
          <tr>
            <td>Departure from Mill Site to Davao </td>
            <td>Date: <u>{{$satSchedString ? $satSchedString->format('Y-m-d') : ''}}</u></td>
            <td>Time: <u>12:15 PM</u></td>            
          </tr>
        </table>
      </td>
    </tr>
     <tr><td>&nbsp;</td></tr>
	  <tr style="font-weight:bold;color:blue;">
	       <td>Seat No.</td>
          <td>ID</td>
          <td>Name</td>
          <td>Department</td>
          <td>Password</td>
          <td>Destination</td>
          <td>Signature</td>
	  </tr>
	  <tr><td>&nbsp;</td></tr>     	              
        @for ($i = 1; $i <= config('constant.totalNumberOfSeat'); $i++)     
            <tr style='background-color:{{$i % 2 ? "#FCF3CF" : "#ffffff"}};'>
                <td style="border-style: solid; border-color: black; border-width: 1px;">{{$i}}</td>
                @if(in_array($i, $bookingSeats)) 
                    <td style="border-style: solid; border-color: black; border-width: 1px;">{{ $bookings[array_search($i, $bookingSeats)]->employee->empId }}</td>
                    <td style="border-style: solid; border-color: black; border-width: 1px;">{{ $bookings[array_search($i, $bookingSeats)]->employee->name }}</td>
                    <td style="border-style: solid; border-color: black; border-width: 1px;">{{ $bookings[array_search($i, $bookingSeats)]->employee->dept }}</td>
                    <td style="border-style: solid; border-color: black; border-width: 1px;">{{ $bookings[array_search($i, $bookingSeats)]->pword }}</td>
                    <td style="border-style: solid; border-color: black; border-width: 1px;">{{ $bookings[array_search($i, $bookingSeats)]->destination }}</td>
                    <td style="border-style: solid; border-color: black; border-width: 1px;">____________</td>
                @else
                    <td style="border-style: solid; border-color: black; border-width: 1px;"></td>
                    <td style="border-style: solid; border-color: black; border-width: 1px;"></td>
                    <td style="border-style: solid; border-color: black; border-width: 1px;"></td>
                    <td style="border-style: solid; border-color: black; border-width: 1px;"></td>
                    <td style="border-style: solid; border-color: black; border-width: 1px;"></td>
                    <td style="border-style: solid; border-color: black; border-width: 1px;"></td>
                @endif
            </tr>
        
        @endfor

      </table>
	  <table style="font-family:Arial;font-size:12px;" width="100%">
	
	<tr><td>&nbsp;</td></tr>
  <tr>
    <td colspan="2">Additional No. of Passengers:_________</td>
    <td colspan="2">Actual No. of Passengers:_________</td>
  </tr>
  <tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
  <tr>
    <td colspan="2">Prepared by:</td>
      
    <td colspan="2">Approved by:</td>
  </tr>
	<tr>
        <!-- <td colspan="2"><br><br><u>RAQUEL S. MACARAYA</u></td> -->
        <td colspan="2"><br><br><u>IAN M. OBLIANDA</u></td>
	
		 <!-- <td colspan="2"><br><br><u>ENGR. EVANGELINE S. SALVAÑA</u></td> -->
     <td colspan="2"><br><br><u>CLAIRE A. AMARILLENTO</u></td>
    </tr>
    <tr>
    <td colspan="2">GSD CARPOOL SUPERVISOR</td>
      
    <!-- <td colspan="2">ENVIRONMENT GEN. SERVICES MANAGER</td> -->
    <td colspan="2">GSD MANAGER</td>
    </tr>
</table>




