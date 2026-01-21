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
            'room_id' => 'required|exists:rooms,id',
            'documents' => 'nullable|array',
        ]);

        $user = Auth::user();

        $final = $this->requestLive->pendingLiveRequest($validated, $user);

        return result($final, 201, 'Заявка успещно отправлена');
    }

    public function index()
    {
        $requests = RequestLive::with(['student', 'room.floor.building'])->get();

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
