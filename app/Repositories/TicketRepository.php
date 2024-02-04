<?php

namespace App\Repositories;

use App\Http\Requests\SportUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\TicketRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Sport;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Repositories\Contracts\ITicketRepository;
use App\Repositories\traits\GlobalFunc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class TicketRepository implements ITicketRepository {

    use GlobalFunc;

    /**
     * Get the sports pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator
    {
        $search = $request->get('query');
        return Ticket::query()
            ->with('subject')
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(!empty($search), function ($query) use ($search) {
                // return $query->where('title', 'like', '%' . $search . '%')
                //     ->orWhere('alias_title','like','%' . $search . '%');
            })
            ->orderBy($request->get('sortBy', 'id'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 25));
    }

    /**
     * Get the sport.
     * @param Ticket $ticket
     * @return Ticket
     */
    public function show(Ticket $ticket) :Ticket
    {
        return Ticket::query()
                ->with('subject')
                ->where('id', $ticket->id)
                ->first();
    }

    /**
     * Store the ticket.
     * @param TicketRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(TicketRequest $request) :JsonResponse
    {

        $ticket = Ticket::create([
            'subject_id'    => $request->input('subject_id'),
            'user_id'       => Auth::user()->id,
        ]);

        $message = TicketMessage::create([
            'ticket_id'    => $ticket->id,
            'file'         => $request->input('file', null),
            'message'      => $request->input('message'),
            'user_id'      => Auth::user()->id,
        ]);

        if ($message) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();
    }

    /**
     * Update the sport.
     * @param SportUpdateRequest $request
     * @param Sport $sport
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(SportUpdateRequest $request, Sport $sport) :JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $sport->user_id);

        $sport = $sport->update([
            'alias_id'      => $request->input('alias_id'),
            'alias_title'   => $request->input('alias_title'),
            'title'         => $request->input('title'),
            'image'         => $request->input('image'),
            'user_id'       => auth()->user()->id,
            'status'        => $request->input('status'),
        ]);

        if ($sport) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
    }

    /**
    * Delete the sport.
    * @param UpdatePasswordRequest $request
    * @param Sport $sport
    * @return JsonResponse
    */
   public function destroy(Sport $sport) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $sport->user_id);

        $sport->delete();

        if ($sport) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }
}
