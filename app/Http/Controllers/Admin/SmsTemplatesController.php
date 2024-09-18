<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\SmsTemplate;
use Tobuli\Helpers\Templates\TemplateManager;
use Tobuli\Repositories\SmsTemplate\SmsTemplateRepositoryInterface;
use Tobuli\Validation\SmsTemplateFormValidator;

class SmsTemplatesController extends BaseController {
    /**
     * @var SmsTemplateRepositoryInterface
     */
    private $smsTemplate;
    private $section = 'sms_templates';
    /**
     * @var SmsTemplateFormValidator
     */
    private $smsTemplateFormValidator;

    function __construct(SmsTemplateRepositoryInterface $smsTemplate, SmsTemplateFormValidator $smsTemplateFormValidator)
    {
        parent::__construct();
        $this->smsTemplate = $smsTemplate;
        $this->smsTemplateFormValidator = $smsTemplateFormValidator;
    }

    public function index() {
        $input = Request::all();

        $input['filter']['user_id'] = $this->user->isReseller() ? $this->user->id : null;

        $items = $this->smsTemplate->searchAndPaginate($input, 'name', 'asc', 20);
        $section = $this->section;

        $canCreate = $this->user->canChangeAppearance();

        return View::make('admin::'.ucfirst($this->section).'.' . (Request::ajax() ? 'table' : 'index'))
            ->with(compact('items', 'input', 'section', 'canCreate'));
    }

    public function create() {
        $this->checkException('sms_templates', 'create');

        $names = SmsTemplate::notOwn($this->user)->orderBy('name')->pluck('name', 'name');

        return View::make('admin::'.ucfirst($this->section).'.create')->with(compact('names'));
    }

    public function store() {
        $this->checkException('sms_templates', 'create');

        $template = SmsTemplate::notOwn($this->user)
            ->where('sms_templates.name', request()->get('name'))
            ->first();

        if (!$template)
            return Response::json(['status' => 0]);

        $clone = new SmsTemplate();
        $clone->user_id = $this->user->id;
        $clone->name = $template->name;
        $clone->title = $template->title;
        $clone->note = $template->note;
        $clone->save();

        return Response::json(['status' => 1]);
    }

    public function edit($id = NULL) {
        $item = $this->smsTemplate->find($id);

        $this->checkException('sms_templates', 'edit', $item);

        $replacers = (new TemplateManager())->loadTemplateBuilder($item->name)->getPlaceholders($item);

        return View::make('admin::'.ucfirst($this->section).'.edit')->with(compact('item', 'replacers'));
    }

    public function update($id) {
        $input = Request::all();

        $item = $this->smsTemplate->find($id);

        $this->checkException('sms_templates', 'edit', $item);

        $this->smsTemplateFormValidator->validate('update', $input, $id);

        $item->update($input);

        return Response::json(['status' => 1]);
    }

    public function destroy($id)
    {
        $item = $this->smsTemplate->find($id);

        $this->checkException('sms_templates', 'remove', $item);

        $item->delete();

        return Response::json(['status' => 1]);
    }
}
