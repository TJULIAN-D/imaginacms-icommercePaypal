<?php

namespace Modules\Icommercepaypal\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Icommercepaypal\Entities\IcommercePaypal;
use Modules\Icommercepaypal\Http\Requests\CreateIcommercePaypalRequest;
use Modules\Icommercepaypal\Http\Requests\UpdateIcommercePaypalRequest;
use Modules\Icommercepaypal\Repositories\IcommercePaypalRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

class IcommercePaypalController extends AdminBaseController
{
    /**
     * @var IcommercePaypalRepository
     */
    private $icommercepaypal;

    public function __construct(IcommercePaypalRepository $icommercepaypal)
    {
        parent::__construct();

        $this->icommercepaypal = $icommercepaypal;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //$icommercepaypals = $this->icommercepaypal->all();

        return view('icommercepaypal::admin.icommercepaypals.index', compact(''));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('icommercepaypal::admin.icommercepaypals.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateIcommercePaypalRequest $request
     * @return Response
     */
    public function store(CreateIcommercePaypalRequest $request)
    {
        $this->icommercepaypal->create($request->all());

        return redirect()->route('admin.icommercepaypal.icommercepaypal.index')
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('icommercepaypal::icommercepaypals.title.icommercepaypals')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  IcommercePaypal $icommercepaypal
     * @return Response
     */
    public function edit(IcommercePaypal $icommercepaypal)
    {
        return view('icommercepaypal::admin.icommercepaypals.edit', compact('icommercepaypal'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  IcommercePaypal $icommercepaypal
     * @param  UpdateIcommercePaypalRequest $request
     * @return Response
     */
    public function update(IcommercePaypal $icommercepaypal, UpdateIcommercePaypalRequest $request)
    {
        $this->icommercepaypal->update($icommercepaypal, $request->all());

        return redirect()->route('admin.icommercepaypal.icommercepaypal.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('icommercepaypal::icommercepaypals.title.icommercepaypals')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  IcommercePaypal $icommercepaypal
     * @return Response
     */
    public function destroy(IcommercePaypal $icommercepaypal)
    {
        $this->icommercepaypal->destroy($icommercepaypal);

        return redirect()->route('admin.icommercepaypal.icommercepaypal.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('icommercepaypal::icommercepaypals.title.icommercepaypals')]));
    }
}
