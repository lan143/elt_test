<?php

namespace App\Providers;

use App\Http\Requests\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class FormRequestServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->resolving(FormRequest::class, function ($request, $app) {
            $this->initializeRequest($request, $app['request']);
        });

        $this->app->afterResolving(FormRequest::class, function ($form) {
            $form->validate();
        });
    }

    protected function initializeRequest(FormRequest $form, Request $current): void
    {
        $files = $current->files->all();
        $files = is_array($files) ? array_filter($files) : $files;
        $form->initialize($current->query->all(), $current->request->all(), $current->attributes->all(), $current->cookies->all(), $files, $current->server->all(), $current->getContent());
        $form->setJson($current->json());
    }
}
