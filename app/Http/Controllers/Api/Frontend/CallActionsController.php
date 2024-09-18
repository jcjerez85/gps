<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Transformers\CallAction\CallActionFullTransformer;
use CustomFacades\Validators\CallActionFormValidator;
use Tobuli\Entities\CallAction;
use Tobuli\Entities\Event;
use FractalTransformer;

class CallActionsController extends BaseController {

    public function __construct()
    {
        if (! settings('plugins.call_actions.status')) {
            abort(404);
        }

        parent::__construct();
    }

    public function index()
    {
        $this->checkException('call_actions', 'view');

        $items = CallAction::paginate(30);

        return response()->json(array_merge(
            ['status' => 1],
            FractalTransformer::paginate($items, CallActionFullTransformer::class)
                ->toArray()
        ));
    }

    public function store()
    {
        $this->checkException('call_actions', 'store');
        CallActionFormValidator::validate('create', $this->data);
        CallAction::create($this->data);

        return response()->json(['status' => 1]);
    }

    public function show($id)
    {
        $item = CallAction::find($id);
        $this->checkException('call_actions', 'view', $item);

        return response()->json(array_merge(
            ['status' => 1],
            FractalTransformer::item($item, CallActionFullTransformer::class)->toArray()
        ));
    }

    public function update($id)
    {
        CallActionFormValidator::validate('update', $this->data);

        $item = CallAction::find($id);
        $this->checkException('call_actions', 'update', $item);
        $item->update($this->data);

        return response()->json(['status' => 1]);
    }

    public function destroy($id)
    {
        $item = CallAction::find($id);
        $this->checkException('call_actions', 'remove', $item);
        $item->delete();

        return response()->json(['status' => 1]);
    }

    public function getResponseTypes()
    {
        return response()->json([
            'status' => 1,
            'types' => CallAction::getResponseTypes(),
        ]);
    }

    public function getEventTypes()
    {
        return response()->json([
            'status' => 1,
            'types' => Event::getTypeTitles(),
        ]);
    }
}
