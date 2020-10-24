<?php

namespace App\Http\Controllers;

use App\Enumerators\UserTypes;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }

    public function index()
    {
        $users = $this->userService->getAll();
        return response($users);
    }

    public function show(int $id)
    {
        $user = $this->userService->findOrFail($id);
        return response($user);
    }

    public function store(Request $request)
    {
        $validatedData = $this->validate($request, [
            'name' => 'required|string|max:255',
            'cpf' => 'required|cpf|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'type' => ['required', Rule::in([UserTypes::CUSTOMER, UserTypes::SELLER])]
        ]);
        $user = $this->userService->save($validatedData);
        return response($user, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $validatedData = $this->validate($request, [
            'name' => 'sometimes|string|max:255',
            'cpf' => 'sometimes|cpf|unique:users',
            'email' => 'sometimes|email|unique:users',
            'password' => 'sometimes|min:8',
            'type' => ['sometimes', Rule::in([UserTypes::CUSTOMER, UserTypes::SELLER])]
        ]);
        $user = $this->userService->updateById($id, $validatedData);
        return response($user);
    }

    public function destroy(int $id)
    {
        $this->userService->deleteById($id);
        return response([], Response::HTTP_NO_CONTENT);
    }
}
