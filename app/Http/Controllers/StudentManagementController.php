<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Room_allocation;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentManagementController extends Controller
{
    // add student
    public function addStudent(Request $request)
    {
        $validatedData = $request->validate([
            'roll_number' => 'required|string|max:10|unique:students,roll_number',
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:students,email',
            'phone_number' => 'required|string|max:15|unique:students,phone_number',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
        ]);

        $student = Student::create($validatedData);

        return response()->json([
            'message' => 'Student added successfully.',
            'data' => $student,
        ], 201);
    }

    // allocate room
    public function allocateRoom(Request $request)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'student_roll_number' => 'required|exists:students,roll_number',
        ]);

        $room = Room::findOrFail($validatedData['room_id']);

        // Check room capacity
        if ($room->allocated >= $room->capacity) {
            return response()->json([
                'message' => 'Room is already at full capacity.',
            ], 400);
        }

        // Check if student is already allocated
        $existingAllocation = Room_allocation::where('student_roll_number', $validatedData['student_roll_number'])->exists();
        if ($existingAllocation) {
            return response()->json([
                'message' => 'Student is already allocated to a room.',
            ], 400);
        }

        // Create room allocation
        Room_allocation::create([
            'room_id' => $validatedData['room_id'],
            'student_roll_number' => $validatedData['student_roll_number'],
            'allocation_date' => now()->toDateString(),
        ]);

        // Update room allocation count
        $room->increment('allocated');

        return response()->json([
            'message' => 'Room allocated successfully.',
        ], 201);
    }

    // view all students
    public function viewAllStudents()
    {
        $students = Student::all();

        return response()->json([
            'data' => $students,
        ]);
    }

    // view student by id
    public function viewStudentById($id)
    {
        $student = Student::findOrFail($id);

        return response()->json([
            'data' => $student,
        ]);
    }

    // update student by id
    public function updateStudentById(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validatedData = $request->validate([
            'roll_number' => 'string|max:10|unique:students,roll_number,' . $student->id,
            'name' => 'string|max:100',
            'email' => 'email|max:100|unique:students,email,' . $student->id,
            'phone_number' => 'string|max:15|unique:students,phone_number,' . $student->id,
            'date_of_birth' => 'date',
            'address' => 'string',
        ]);

        $student->update($validatedData);

        return response()->json([
            'message' => 'Student updated successfully.',
            'data' => $student,
        ]);
    }

    // delete student by id
    public function deleteStudentById($id)
    {
        $student = Student::findOrFail($id);

        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully.',
        ]);
    }
}
