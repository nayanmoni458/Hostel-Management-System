<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // create room
    public function createRoom(Request $request)
    {
        $validatedData = $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms,room_number',
            'capacity' => 'required|integer|min:1|max:3',
        ]);

        $validatedData['allocated'] = 0; // Default allocated students is 0

        $room = Room::create($validatedData);

        return response()->json([
            'message' => 'Room added successfully.',
            'data' => $room,
        ], 201);
    }

    // view all rooms
    public function viewRooms()
    {
        $rooms = Room::all();

        return response()->json([
            'message' => 'Rooms fetched successfully.',
            'data' => $rooms,
        ]);
    }

    // view room by id
    public function viewRoom($id)
    {
        $room = Room::findOrFail($id);

        return response()->json([
            'message' => 'Room fetched successfully.',
            'data' => $room,
        ]);
    }

    // update room
    public function updateRoom(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $validatedData = $request->validate([
            'room_number' => 'nullable|string|max:10|unique:rooms,room_number,' . $id,
            'capacity' => 'nullable|integer|min:1|max:3',
        ]);

        $room->update($validatedData);

        return response()->json([
            'message' => 'Room updated successfully.',
            'data' => $room,
        ]);
    }

    // delete room
    public function deleteRoom($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return response()->json([
            'message' => 'Room deleted successfully.',
        ]);
    }

}
