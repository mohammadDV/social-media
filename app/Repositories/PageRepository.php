<?php

namespace App\Repositories;

use App\Http\Requests\PageRequest;
use App\Http\Requests\PageUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Models\Page;
use App\Repositories\Contracts\IPageRepository;
use App\Repositories\traits\GlobalFunc;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PageRepository implements IPageRepository {

    use GlobalFunc;

    /**
     * Get the page pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator
    {

        $search = $request->get('query');
        return Page::query()
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('title', 'like', '%' . $search . '%');
            })
            ->orderBy($request->get('sortBy', 'id'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 25));
    }


    /**
     * Get active pages.
     * @return Collection
     */
    public function getActivePages() :Collection
    {
        return cache()->remember("pages.active", now()->addMinutes(10), function () {
            return Page::query()
                ->where('status', 1)
                ->orderBy('priority', 'asc')
                ->get();
        });
    }

    /**
     * Get the active page.
     * @param string $slug
     * @return Page|null
     */
    public function getActivePage(string $slug) :Page|null
    {
        return cache()->remember("page.active", now()->addMinutes(config('cache.default_min')), function () use ($slug) {
            return Page::query()
                ->where('status', 1)
                ->where('slug', trim($slug))
                ->orderBy('priority', 'asc')
                ->first();
        });
    }

    /**
     * Get the page info.
     * @param Page $page
     * @return Matches
     */
    public function show(Page $page) :Page
    {
        return $page;
    }

    /**
     * Store the Page.
     * @param PageRequest $request
     * @return JsonResponse
     */
    public function store(PageRequest $request) :JsonResponse
    {
        $this->checkLevelAccess();

        $page = Page::create([
            'title'         => $request->input('title'),
            'content'       => $request->input('content'),
            'image'         => $request->input('image'),
            'user_id'       => Auth::user()->id,
            'status'        => $request->input('status'),
            'priority'      => $request->input('priority')
        ]);

        if ($page) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();

    }

    /**
     * Update the page.
     * @param PageRequest $request
     * @param Page $page
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(PageUpdateRequest $request, Page $page) :JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $page->user_id);

        $page->update([
            'title'         => $request->input('title'),
            'content'       => $request->input('content'),
            'image'         => $request->input('image'),
            'user_id'       => auth()->user()->id,
            'status'        => $request->input('status'),
            'priority'      => $request->input('priority'),
        ]);

        if ($page) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();

    }

    /**
    * Delete the page.
    * @param Page $page
    * @return JsonResponse
    */
   public function destroy(Page $page) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $page->user_id);

        $page->delete();

        if ($page) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }
}
