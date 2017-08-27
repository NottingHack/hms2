<?php

namespace App\Http\Controllers;

use HMS\Entities\Meta;
use Illuminate\Http\Request;
use HMS\Repositories\MetaRepository;

class MetaController extends Controller
{
    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * @param MetaRepository $metaRepository
     */
    public function __construct(MetaRepository $metaRepository)
    {
        $this->metaRepository = $metaRepository;

        $this->middleware('can:meta.view')->only(['index']);
        $this->middleware('can:meta.edit')->only(['edit', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $metas = $this->metaRepository->paginateAll();

        return view('meta.index')
            ->with(['metas' => $metas]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function edit($key)
    {
        if ( ! $this->metaRepository->has(
            $key)) {
            flash('Key \''.$key.'\' not found', 'warning');

            return redirect()->route('metas.index');
        }

        $value = $this->metaRepository->get($key);

        return view('meta.edit')
            ->with([
                    'key' => $key,
                    'value' => $value,
                    ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $key)
    {
        if ( ! $this->metaRepository->has(
            $key)) {
            flash('Key \''.$key.'\' not found', 'warning');

            return redirect()->route('metas.index');
        }

        // TODO: validate string in any way needed
        $this->validate($request, [
            'value' => 'required|string|max:255',
        ]);

        $this->metaRepository->set($key, $request['value']);

        flash()->success('Key \''.$key.'\' updated.');

        return redirect()->route('metas.index');
    }
}
