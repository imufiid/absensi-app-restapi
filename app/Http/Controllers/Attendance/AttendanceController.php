<?php

namespace App\Http\Controllers\Attendance;

use App\Attendence;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the data attedance.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $validator = Validator::make(request()->all(), [
            "user_id"    => "required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_BAD_REQUEST,
                    "failed",
                    "Failed"
                ),
                'data' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $dataAttendance = Attendence::where("user_id", request("user_id"))->get();

        if ($dataAttendance != null) {
            return response()->json([
                "meta" => object_meta(
                    Response::HTTP_OK,
                    "success",
                    "List of Data Attendance"
                ),
                "data" => $dataAttendance
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                "meta" => object_meta(
                    Response::HTTP_NOT_FOUND,
                    "failed",
                    "Failed for Attendance"
                ),
                "data" => null
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Display the specified data attendance.
     *
     * @param  \App\Attendence  $attendence
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_employee' => "required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_BAD_REQUEST,
                        "failed",
                        "Failed"
                    ),
                    'data' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }


            $idEmploye = request("id_employee");
            $today = date("d-m-Y");

            $attendanceToday = Attendence::where('user_id', $idEmploye)
                ->where('date', $today)->first();

            if ($attendanceToday == null) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_NOT_FOUND,
                        "failed",
                        "Belum Absen hari ini"
                    ),
                    'data' => null
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_OK,
                        "success",
                        "Attendance Today"
                    ),
                    'data' => $attendanceToday
                ], Response::HTTP_OK);
            }
        } catch (Throwable $e) {
            $data = [
                "error" => $e
            ];
            return response()->json([
                "meta" => object_meta(
                    Response::HTTP_EXPECTATION_FAILED,
                    "error",
                    "Error Handling"
                ),
                "data" => $data
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    /**
     * Employe has been go home.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Attendence  $attendence
     * @return \Illuminate\Http\Response
     */
    public function scan(Request $request)
    {
        try {
            // validation
            $validator = Validator::make($request->all(), [
                "id_employee" => "required"
            ]);

            // check validation
            if ($validator->fails()) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_BAD_REQUEST,
                        "failed",
                        "Failed"
                    ),
                    'data' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            // data request
            $idEmploye = request("id_employee");
            $time = time();

            // get date this day
            $currentDate = date('d-m-Y');
            $checkerAttendance = Attendence::where('date', $currentDate)
                ->first();

            if ($checkerAttendance == null) {
                // is comming
                $time = time();
                $attendance = Attendence::create([
                    "user_id"       => $idEmploye,
                    "date"          => date("d-m-Y", $time),
                    "time_comes"    => date("H:i:s", $time),
                    "time_gohome"   => 0
                ]);

                return response()->json([
                    "meta" => object_meta(
                        Response::HTTP_CREATED,
                        "success",
                        "Success for Attendance Comming"
                    ),
                    "data" => $attendance
                ], Response::HTTP_CREATED);
            } else {
                // is go home

                if ($checkerAttendance->time_gohome != 0) {
                    return response()->json([
                        "meta" => object_meta(
                            Response::HTTP_BAD_REQUEST,
                            "failed",
                            "Anda Sudah Absen Pulang"
                        ),
                        "data" => null
                    ], Response::HTTP_BAD_REQUEST);
                }

                // data for updating
                $data = [
                    "time_gohome" => date("H:i:s", $time),
                ];
                $attendance = Attendence::where('date', $currentDate)
                    ->where('user_id', $idEmploye)
                    ->update($data);

                if ($attendance > 0) {
                    return response()->json([
                        "meta" => object_meta(
                            Response::HTTP_CREATED,
                            "success",
                            "Success for Go Home Attendance"
                        ),
                        "data" => $attendance
                    ], Response::HTTP_CREATED);
                } else {
                    return response()->json([
                        "meta" => object_meta(
                            Response::HTTP_BAD_REQUEST,
                            "failed",
                            "Failed for Go Home Attendance"
                        ),
                        "data" => $attendance
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
        } catch (Throwable $e) {
            $data = [
                "error" => $e
            ];
            return response()->json([
                "meta" => object_meta(
                    Response::HTTP_BAD_REQUEST,
                    "error",
                    "Failed for Attendance"
                ),
                "data" => $data
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
