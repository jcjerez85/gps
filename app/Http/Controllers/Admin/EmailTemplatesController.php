<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\EmailTemplate;
use Tobuli\Helpers\Templates\TemplateManager;
use Tobuli\Repositories\EmailTemplate\EmailTemplateRepositoryInterface;
use Tobuli\Validation\EmailTemplateFormValidator;

class EmailTemplatesController extends BaseController {
    /**
     * @var EmailTemplateRepositoryInterface
     */
    private $emailTemplate;
    private $section = 'email_templates';
    /**
     * @var EmailTemplateFormValidator
     */
    private $emailTemplateFormValidator;

    function __construct(EmailTemplateRepositoryInterface $emailTemplate, EmailTemplateFormValidator $emailTemplateFormValidator)
    {
        parent::__construct();
        $this->emailTemplate = $emailTemplate;
        $this->emailTemplateFormValidator = $emailTemplateFormValidator;
    }

    public function index() {
        $input = Request::all();

        $input['filter']['user_id'] = $this->user->isReseller() ? $this->user->id : null;

        $items = $this->emailTemplate->searchAndPaginate($input, 'name', 'asc', 20);
        $section = $this->section;

        $canCreate = $this->user->canChangeAppearance();

        return View::make('admin::'.ucfirst($this->section).'.' . (Request::ajax() ? 'table' : 'index'))
            ->with(compact('items', 'input', 'section', 'canCreate'));
    }

    public function create() {
        $this->checkException('email_templates', 'create');

        $names = EmailTemplate::notOwn($this->user)->orderBy('name')->pluck('name', 'name');

        return View::make('admin::'.ucfirst($this->section).'.create')->with(compact('names'));
    }

    public function store() {
        $this->checkException('email_templates', 'create');

        $template = EmailTemplate::notOwn($this->user)
            ->where('email_templates.name', request()->get('name'))
            ->first();

        if (!$template)
            return Response::json(['status' => 0]);

        $clone = new EmailTemplate();
        $clone->user_id = $this->user->id;
        $clone->name = $template->name;
        $clone->title = $template->title;
        $clone->note = $template->note;
        $clone->save();

        return Response::json(['status' => 1]);
    }

    public function edit($id = NULL) {
        $item = $this->emailTemplate->find($id);

        $this->checkException('email_templates', 'edit', $item);

        $replacers = (new TemplateManager())->loadTemplateBuilder($item->name)->getPlaceholders($item);

        return View::make('admin::'.ucfirst($this->section).'.edit')->with(compact('item', 'replacers'));
    }

    public function update($id) {
        $input = Request::all();

        $item = $this->emailTemplate->find($id);

        $this->checkException('email_templates', 'edit', $item);

        $this->emailTemplateFormValidator->validate('update', $input, $id);

        $item->update($input);

        return Response::json(['status' => 1]);
    }

    public function destroy($id)
    {
        $item = $this->emailTemplate->find($id);

        $this->checkException('email_templates', 'remove', $item);

        $item->delete();

        return Response::json(['status' => 1]);
    }
}
