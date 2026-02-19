<?php

namespace Modules\Requests\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Requests\Models\RequestLive;
use Modules\Dormitory\Models\Room;
use Illuminate\Support\Facades\Auth;
use Modules\Requests\Services\RequestLiveService;

class RequestLiveController extends Controller
{
    public function __construct(
        private RequestLiveService $requestLive
    ){}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'preferred_room_id' => 'required|exists:rooms,id',
            'documents' => 'nullable|array',
            'documents.*.type' => 'required_with:documents|string|max:255',
            'documents.*.path' => 'required_with:documents|string|max:2048',
        ]);

        $user = Auth::user();

        $final = $this->requestLive->pendingLiveRequest($validated, $user);

        return result($final, 201, 'Заявка успещно отправлена');
    }

    public function index()
    {
        $requests = RequestLive::with(['student', 'preferredRoom.floor.building', 'documents'])->get();

        return result($requests, 200, 'Запросы студентов');
    }


    public function approve($id)
    {
        $requestForLive = $this->requestLive->approveByManager($id);

        return result($requestForLive, 200, 'Заявка успешна приянта');
    }

    public function reject($id)
    {
        $requestLive = $this->requestLive->rejectByManager($id);

        return result($requestLive, 200, 'Запрос успешно отклонен');
    }
}
