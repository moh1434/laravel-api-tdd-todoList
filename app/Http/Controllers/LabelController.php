<?php

namespace App\Http\Controllers;

use App\Http\Requests\LabelRequest;
use App\Models\Label;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LabelController extends Controller
{
    public function store(LabelRequest $request)
    {
        return Label::create($request->validated());
    }

    public function destroy(Label $label)
    {
        $label->delete();
        return response('', Response::HTTP_NO_CONTENT);
    }
}
