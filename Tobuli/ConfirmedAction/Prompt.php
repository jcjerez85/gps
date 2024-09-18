<?php

namespace Tobuli\ConfirmedAction;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Tobuli\ConfirmedAction\TextsModifier\DefaultModifier;
use Tobuli\ConfirmedAction\TextsModifier\DeleteModifier;
use Tobuli\ConfirmedAction\TextsModifier\TextsModifierInterface;

class Prompt
{
    /**
     * @var TextsModifierInterface[]
     */
    private static $textsModifiers = [
        DeleteModifier::class,
        DefaultModifier::class,
    ];

    private $url;
    private $method;
    private $input = [];
    private $texts = [];
    private $respondJson = false;

    public function __construct(string $url, string $method, array $input)
    {
        $this->url = $url;
        $this->method = $method;
        $this->input = $input;
    }

    public static function makeFromRequest(Request $request): self
    {
        $input = $request->input();

        $prompt = new static(
            $input['_url'] ?? $request->url(),
            $request->method(),
            $input
        );

        return $prompt->adjustTextsToRequest($request);
    }

    public function buildResponse()
    {
        unset($this->input['_method']);

        $data = [
            'url' => $this->url . (strpos($this->url, '?') ? '&' : '?') . 'action=proceed',
            'method' => $this->method,
            'input' => $this->input,
            'title' => $this->texts['title'],
            'description' => $this->texts['description'],
        ];

        $view = view('admin::Layouts.partials.confirmed_action')->with($data);

        return response(
            $this->respondJson ? json_encode($data + ['view' => $view->render()]) : $view
        )->setStatusCode(Response::HTTP_I_AM_A_TEAPOT);
    }

    private function adjustTextsToRequest(Request $request): self
    {
        foreach (self::$textsModifiers as $modifier) {
            (new $modifier())->modify($this->texts, $request);
        }

        return $this;
    }

    public static function isAvailable(Request $request): bool
    {
        return !self::isApiRequest() && !self::isActionConfirmed($request);
    }

    private static function isApiRequest(): bool
    {
        return (bool)Config::get('tobuli.api');
    }

    private static function isActionConfirmed(Request $request): bool
    {
        return $request->get('action') === 'proceed';
    }
    
    public function setTitle(string $title): self
    {
        $this->texts['title'] = $title;

        return $this;
    }
    
    public function setDescription(string $description): self
    {
        $this->texts['description'] = $description;

        return $this;
    }

    public function setRespondJson(bool $respondJson): self
    {
        $this->respondJson = $respondJson;

        return $this;
    }
}