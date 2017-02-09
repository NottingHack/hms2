<?php

namespace App\Http\Controllers;

use HMS\Entities\Link;
use HMS\Factories\LinkFactory;
use App\Http\Requests\LinkRequest;
use HMS\Repositories\LinkRepository;

class LinksController extends Controller
{
    /**
     * @var LinkRepository
     */
    protected $linkRepository;

    /**
     * @param LinkRepository $linkRepository
     */
    public function __construct(LinkRepository $linkRepository)
    {
        $this->linkRepository = $linkRepository;

        $this->middleware('can:link.view')->only(['index', 'show']);
        $this->middleware('can:link.create')->only(['create', 'store']);
        $this->middleware('can:link.edit')->only(['edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $links = $this->linkRepository->paginateAll();

        return view('links.index')->with(['links' => $links]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $link = new Link();

        return view('links.create')->with($link->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LinkRequest $request)
    {
        $link = LinkFactory::createFromRequest($request);
        $this->linkRepository->save($link);
        flash()->success('Link \''.$link->getName().'\' created.');

        return redirect()->route('links.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Link  $link
     * @return \Illuminate\Http\Response
     */
    public function edit(Link $link)
    {
        return view('links.edit')->with($link->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Link  $link
     * @return \Illuminate\Http\Response
     */
    public function update(LinkRequest $request, Link $link)
    {
        $link->updateWithRequest($request);
        $this->linkRepository->save($link);
        flash()->success('Link \''.$link->getName().'\' updated.');

        return redirect()->route('links.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Link  $link
     * @return \Illuminate\Http\Response
     */
    public function destroy(Link $link)
    {
        $this->linkRepository->remove($link);
        flash()->success('Link \''.$link->getName().'\' removed.');

        return redirect()->route('links.index');
    }
}
