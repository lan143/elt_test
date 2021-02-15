<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Repositories\MessagesRepositoryInterface;
use App\Services\MessageService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\Redirector;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MessagesController extends Controller
{
    /**
     * @var MessagesRepositoryInterface
     */
    private MessagesRepositoryInterface $repository;
    /**
     * @var MessageService
     */
    private MessageService $service;

    /**
     * Create a new controller instance.
     *
     * @param MessagesRepositoryInterface $repository
     * @param MessageService $service
     */
    public function __construct(MessagesRepositoryInterface $repository, MessageService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        var_export($this->repository->getAll());

        return view('messages.index', [
            'messages' => $this->repository->getAll(),
        ]);
    }

    public function create(CreateMessageRequest $request): Response|Redirector|RedirectResponse
    {
        try {
            $this->service->create($request->input('message'), $request->input('parent'));
        } catch (DomainException $e) {
            return new Response($e->getMessage());
        }

        return redirect('/');
    }

    public function update(UpdateMessageRequest $request, string $guid): Response|Redirector|RedirectResponse
    {
        $message = $this->repository->findByGuid($guid);

        if ($message === null) {
            throw new NotFoundHttpException('Message not found');
        }

        $this->service->update($message, $request->input('message'));

        return redirect('/');
    }
}
