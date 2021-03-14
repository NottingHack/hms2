<?php

namespace App\Http\Controllers;

use HMS\Entities\Link;
use HMS\Factories\LinkFactory;
use HMS\Repositories\LinkRepository;
use Illuminate\Http\Request;

class LinksController extends Controller
{
    /**
     * @var LinkRepository
     */
    protected $linkRepository;

    /**
     * @var LinkFactory
     */
    protected $linkFactory;

    /**
     * Create a new controller instance.
     *
     * @param LinkRepository $linkRepository
     * @param LinkFactory $linkFactory
     */
    public function __construct(LinkRepository $linkRepository, LinkFactory $linkFactory)
    {
        $this->linkRepository = $linkRepository;
        $this->linkFactory = $linkFactory;

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

        return view('links.create')->with('link', $link);
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
        $this->validate($request, [
            'name' => 'required|unique:HMS\Entities\Link',
            'link' => 'required|url',
        ]);

        $link = $this->linkFactory->createFromRequest($request);
        $this->linkRepository->save($link);
        flash()->success('Link \'' . $link->getName() . '\' created.');

        return redirect()->route('links.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Link $link
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Link $link)
    {
        return view('links.edit')->with('link', $link);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Link $link
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Link $link)
    {
        $this->validate($request, [
            'name' => 'required',
            'link' => 'required|url',
        ]);

        $link->setName($request['name']);
        $link->setLink($request['link']);
        $link->setDescription($request['description']);

        $this->linkRepository->save($link);
        flash()->success('Link \'' . $link->getName() . '\' updated.');

        return redirect()->route('links.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Link $link
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Link $link)
    {
        $this->linkRepository->remove($link);
        flash()->success('Link \'' . $link->getName() . '\' removed.');

        return redirect()->route('links.index');
    }
}
