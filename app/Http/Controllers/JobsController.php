<?php

namespace App\Http\Controllers;

use App\Jobs\VisitJob;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function index(): void
    {
        VisitJob::dispatch("Esta funcionando tudo")
            ->onQueue('high')->delay(now()->addSecond(10));
    }

    public function jobDefault():void
    {
        VisitJob::dispatch('Este é um job padrão')
            ->onQueue('default')->delay(now()->addSecond(10));
    }

    public function jobLow():void
    {
        VisitJob::dispatch('Este é um job lento')
            ->onQueue('low')->delay(now()->addSecond(12));
    }
}
