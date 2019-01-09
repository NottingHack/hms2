<?php

namespace App\Http\Controllers\Tools;

use HMS\Tools\ToolManager;
use HMS\Entities\Tools\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\Tools\ToolRepository;
use HMS\Repositories\Tools\BookingRepository;

class ToolController extends Controller
{
    /**
     * @var ToolRepository
     */
    protected $toolRepository;

    /**
     * @var ToolManager
     */
    protected $toolManager;

    /**
     * @var BookingRepository
     */
    protected $bookingRepository;

    /**
     * Create a new controller instance.
     *
     * @param ToolRepository    $toolRepository
     * @param ToolManager       $toolManager
     * @param BookingRepository $bookingRepository
     */
    public function __construct(ToolRepository $toolRepository, ToolManager $toolManager, BookingRepository $bookingRepository)
    {
        $this->toolRepository = $toolRepository;
        $this->toolManager = $toolManager;
        $this->bookingRepository = $bookingRepository;

        $this->middleware('can:tools.view')->only(['index', 'show']);
        $this->middleware('can:tools.create')->only(['create', 'store']);
        $this->middleware('can:tools.edit')->only(['edit', 'update']);
        $this->middleware('can:tools.destroy')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tools = $this->toolRepository->findAll();
        $nextBookings = [];
        foreach ($tools as $tool) {
            $nextBookings[$tool->getId()] = $this->bookingRepository->nextForTool($tool);
        }

        return view('tools.tool.index')
            ->with('tools', $tools)
            ->with('nextBookings', $nextBookings);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tools.tool.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'toolName'      => 'required|string|max:20|unique:HMS\Entities\Tools\Tool,name',
            'restricted'    => 'sometimes|required',
            'cost'          => 'required|integer|min:0',
            'bookingLength' => 'required|integer|min:0',
            'lengthMax'     => 'required|integer|min:0',
            'bookingsMax'    => 'required|integer|min:1',
        ]);

        $tool = $this->toolManager->create(
            $request->toolName,
            isset($request->restricted) ? true : false,
            $request->cost,
            $request->bookingLength,
            $request->lengthMax,
            $request->bookingsMax
        );
        flash('Tool \''.$tool->getName().'\' created.')->success();

        return redirect()->route('tools.show', ['tool' => $tool->getId()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Tool  $tool
     * @return \Illuminate\Http\Response
     */
    public function show(Tool $tool)
    {
        return view('tools.tool.show')->with('tool', $tool);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Tool  $tool
     * @return \Illuminate\Http\Response
     */
    public function edit(Tool $tool)
    {
        return view('tools.tool.edit')->with('tool', $tool);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Tool  $tool
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tool $tool)
    {
        $this->validate($request, [
            'toolName'      => 'required|string|max:20|unique:HMS\Entities\Tools\Tool,name,'.$tool->getId(),
            'restricted'    => 'sometimes|required',
            'cost'          => 'required|integer|min:0',
            'bookingLength' => 'required|integer|min:0',
            'lengthMax'     => 'required|integer|min:0',
            'bookingsMax'    => 'required|integer|min:1',
        ]);

        $this->toolManager->update($tool, $request->all());
        flash('Tool \''.$tool->getName().'\' updated.')->success();

        return redirect()->route('tools.show', ['tool' => $tool->getId()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Tool  $tool
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tool $tool)
    {
        $this->toolManager->removeTool($tool);
        flash('Tool \''.$tool->getName().'\' removed.')->success();

        return redirect()->route('tools.index');
    }
}
