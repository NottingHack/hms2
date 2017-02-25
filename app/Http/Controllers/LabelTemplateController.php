<?php

namespace App\Http\Controllers;

use HMS\Entities\LabelTemplate;
use HMS\Factories\LabelTemplateFactory;
use App\Http\Requests\LabelTemplateRequest;
use HMS\Repositories\LabelTemplateRepository;

class LabelTemplateController extends Controller
{
    /**
     * @var LabelTemplateRepository
     */
    protected $labelTemplateRepository;

    /**
     * @param LabelTemplateRepository $labelTemplateRepository
     */
    public function __construct(LabelTemplateRepository $labelTemplateRepository)
    {
        $this->labelTemplateRepository = $labelTemplateRepository;

        $this->middleware('can:labelTemplate.view')->only(['index', 'show']);
        $this->middleware('can:labelTemplate.create')->only(['create', 'store']);
        $this->middleware('can:labelTemplate.edit')->only(['edit', 'update', 'destroy']);
        $this->middleware('can:labelTemplate.print')->only(['showPrint', 'print']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $labelTemplates = $this->labelTemplateRepository->paginateAll();

        return view('labelTemplates.index')->with(['labelTemplates' => $labelTemplates]);
    }

    /**
     * Show a specific resource.
     *
     * @param LabelTemplate $labelTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(LabelTemplate $label)
    {
        return view('labelTemplates.show')->with($label->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $label = new LabelTemplate();

        return view('labelTemplates.create')->with($label->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LabelTemplateRequest $request)
    {
        $label = LabelTemplateFactory::createFromRequest($request);
        $this->labelTemplateRepository->save($label);
        flash()->success('Label Template \''.$label->getTemplateName().'\' created.');

        return redirect()->route('labels.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  LabelTemplate  $label
     * @return \Illuminate\Http\Response
     */
    public function edit(LabelTemplate $label)
    {
        return view('labelTemplates.edit')->with($label->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  LabelTemplate  $label
     * @return \Illuminate\Http\Response
     */
    public function update(LabelTemplateRequest $request, LabelTemplate $label)
    {
        $label->updateWithRequest($request);
        $this->labelTemplateRepository->save($label);
        flash()->success('Label Template \''.$label->getTemplateName().'\' updated.');

        return redirect()->route('labels.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  LabelTemplate  $label
     * @return \Illuminate\Http\Response
     */
    public function destroy(LabelTemplate $label)
    {
        $this->labelTemplateRepository->remove($label);
        flash()->success('Label Template \''.$label->getTemplateName().'\' removed.');

        return redirect()->route('labels.index');
    }
}
