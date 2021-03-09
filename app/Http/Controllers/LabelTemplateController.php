<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use HMS\Entities\LabelTemplate;
use App\Events\Labels\ManualPrint;
use HMS\Factories\LabelTemplateFactory;
use HMS\Repositories\LabelTemplateRepository;

class LabelTemplateController extends Controller
{
    /**
     * @var LabelTemplateRepository
     */
    protected $labelTemplateRepository;

    /**
     * @var LabelTemplateFactory
     */
    protected $labelTemplateFactory;

    /**
     * Create a new controller instance.
     *
     * @param LabelTemplateRepository $labelTemplateRepository
     * @param LabelTemplateFactory $labelTemplateFactory
     */
    public function __construct(
        LabelTemplateRepository $labelTemplateRepository,
        LabelTemplateFactory $labelTemplateFactory
    ) {
        $this->labelTemplateRepository = $labelTemplateRepository;
        $this->labelTemplateFactory = $labelTemplateFactory;

        $this->middleware('feature:label_printer');
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
     * @param LabelTemplate $label
     *
     * @return \Illuminate\Http\Response
     */
    public function show(LabelTemplate $label)
    {
        return view('labelTemplates.show')->with('label', $label);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $label = new LabelTemplate();

        return view('labelTemplates.create')->with('label', $label);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $label = $this->labelTemplateFactory->createFromRequest($request);
        $this->labelTemplateRepository->save($label);
        flash()->success('Label Template \'' . $label->getTemplateName() . '\' created.');

        return redirect()->route('labels.show', ['label' => $label->getTemplateName()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LabelTemplate $label
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LabelTemplate $label)
    {
        return view('labelTemplates.edit')->with('label', $label);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param LabelTemplate $label
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LabelTemplate $label)
    {
        $label->setTemplateName($request['templateName']);
        $label->setTemplate($request['template']);

        $this->labelTemplateRepository->save($label);
        flash()->success('Label Template \'' . $label->getTemplateName() . '\' updated.');

        return redirect()->route('labels.show', ['label' => $label->getTemplateName()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param LabelTemplate $label
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(LabelTemplate $label)
    {
        $this->labelTemplateRepository->remove($label);
        flash()->success('Label Template \'' . $label->getTemplateName() . '\' removed.');

        return redirect()->route('labels.index');
    }

    /**
     * Preper a label template for printing,
     * A label template may require some user provided field.
     *
     * @param LabelTemplate $label
     *
     * @return \Illuminate\Http\Response
     */
    public function showPrint(LabelTemplate $label)
    {
        // work out needed fields
        preg_match_all('/{{ ?\$(\w+) ?}}/', $label->getTemplate(), $matches);

        return view('labelTemplates.print')
            ->with('label', $label)
            ->with(['fields' => $matches[1]]);
    }

    /**
     * Send a label off for printing.
     *
     * @param Request $request
     * @param LabelTemplate $label
     *
     * @return \Illuminate\Http\Response
     */
    public function print(Request $request, LabelTemplate $label)
    {
        $this->validate($request, [
            'copiesToPrint' => 'required|integer',
        ]);

        $input = $request->all();
        unset($input['_token']);
        unset($input['copiesToPrint']);
        event(
            new ManualPrint(
                $label->getTemplateName(),
                $input,
                (int) $request->input('copiesToPrint')
            )
        );

        flash()->success('Label Template \'' . $label->getTemplateName() . '\' sent to printer.');

        return redirect()->route('labels.show', ['label' => $label->getTemplateName()]);
    }
}
