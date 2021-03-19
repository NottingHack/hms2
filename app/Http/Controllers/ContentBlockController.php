<?php

namespace App\Http\Controllers;

use HMS\Entities\ContentBlock;
use HMS\Repositories\ContentBlockRepository;
use Illuminate\Http\Request;

class ContentBlockController extends Controller
{
    /**
     * @var ContentBlockRepository
     */
    protected $contentBlockRepository;

    /**
     * Create a new controller instance.
     *
     * @param ContentBlockRepository $contentBlockRepository
     */
    public function __construct(ContentBlockRepository $contentBlockRepository)
    {
        $this->contentBlockRepository = $contentBlockRepository;

        $this->middleware('can:contentBlock.view')->only(['index', 'show']);
        $this->middleware('can:contentBlock.edit')->only(['edit', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contentBlocks = $this->contentBlockRepository->paginateAll();

        return view('contentBlocks.index')->with('contentBlocks', $contentBlocks);
    }

    /**
     * Display the specified resource.
     *
     * @param  \HMS\Entities\ContentBlock  $contentBlock
     * @return \Illuminate\Http\Response
     */
    public function show(ContentBlock $contentBlock)
    {
        return view('contentBlocks.show')->with('contentBlock', $contentBlock);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \HMS\Entities\ContentBlock  $contentBlock
     * @return \Illuminate\Http\Response
     */
    public function edit(ContentBlock $contentBlock)
    {
        return view('contentBlocks.edit')->with('contentBlock', $contentBlock);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \HMS\Entities\ContentBlock  $contentBlock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContentBlock $contentBlock)
    {
        $validatedData = $request->validate([
            'content' => 'nullable|string',
        ]);

        $contentBlock->setContent($validatedData['content']);

        $this->contentBlockRepository->save($contentBlock);

        flash('Content Block updated')->success();

        return redirect()->route('content-blocks.show', $contentBlock->getId());
    }
}
