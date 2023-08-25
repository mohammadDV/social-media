<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Construct the controller
     *
     * @param IProductRepository $repository
     */
    public function __construct(private IProductRepository $repository)
    {
    }

    public function index(Request $request) {
        return response()->json($this->repository->index()
    }
}
