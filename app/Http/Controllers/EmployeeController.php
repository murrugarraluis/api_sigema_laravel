<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\SafeCredentialsResource;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {

        $employees = Employee::all()->sortByDesc('created_at');
        if ($request->type) {
            $employees = Employee::where('type', $request->type)->get()->sortByDesc('created_at');
        }
        if ($request->type && $request->turn) {
            $employees = Employee::where('type', $request->type)
                ->where('turn', $request->turn)
                ->get()->sortByDesc('created_at');
        }
        return EmployeeResource::collection($employees);
    }

    public function index_withoutuser(): AnonymousResourceCollection
    {
        $employees = Employee::doesntHave('user')->get()->sortByDesc('created_at');
        return EmployeeResource::collection($employees);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse|Response|object
     */
    public function store(EmployeeRequest $request)
    {
        DB::beginTransaction();
        try {
//          CREATE EMPLOYEE
            $employee = Employee::create([
                'document_number' => $request->document_number,
                'name' => $request->name,
                'lastname' => $request->lastname,
                'personal_email' => $request->personal_email,
                'phone' => $request->phone,
                'address' => $request->address,
                'position_id' => $request->position["id"],
                'document_type_id' => $request->document_type["id"],
                'type' => $request->type,
                'turn' => $request->turn,
                'native_language' => $request->native_language,
            ]);
            DB::commit();
            return (new EmployeeResource($employee))
                ->additional(['message' => 'Employee created.'])
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            DB::rollback();
            throw new BadRequestException($e->getMessage());
        }
    }

    /**
     * Generate credentials Employee.
     *
     * @param Employee $employee
     * @return SafeCredentialsResource
     */
    public function generate_safe_credentials(Employee $employee): SafeCredentialsResource
    {
        $username = trim(substr(strtolower($employee->name), 0, 1) . substr(strtolower($employee->lastname), 0, strpos($employee->lastname, " ")));
        $count_users = User::where('email', 'like', '%' . $username . '%')->count();
        if ($count_users > 0) $username = $username . $count_users;

        $email = $username . "@jextechnologies.com";
        $password = $employee->document_number;
        $credentials = [
            "email" => $email,
            "password" => $password
        ];

        return (new SafeCredentialsResource($credentials))->additional(['message' => 'Safe credentials generated.']);
    }

    /**
     * Display the specified resource.
     *
     * @param Employee $employee
     * @return EmployeeResource
     */
    public function show(Employee $employee): EmployeeResource
    {
        return new EmployeeResource($employee);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Employee $employee
     * @return EmployeeResource
     */
    public function update(EmployeeRequest $request, Employee $employee): EmployeeResource
    {
        DB::beginTransaction();
        try {
//          UPDATE EMPLOYEE
            $employee->update([
                'document_number' => $request->document_number,
                'name' => $request->name,
                'lastname' => $request->lastname,
                'personal_email' => $request->personal_email,
                'phone' => $request->phone,
                'address' => $request->address,
                'position_id' => $request->position["id"],
                'document_type_id' => $request->document_type["id"],
                'type' => $request->type,
                'turn' => $request->turn,
                'native_language' => $request->native_language,

            ]);
            DB::commit();
            return (new EmployeeResource($employee))->additional(['message' => 'Employee updated.']);
        } catch (\Exception $e) {
            DB::rollback();
            throw new BadRequestException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Employee $employee
     * @return JsonResponse
     */
    public function destroy(Employee $employee): JsonResponse
    {
        $employee->delete();
        return response()->json(['message' => 'Employee removed.'], 200);
    }
}
