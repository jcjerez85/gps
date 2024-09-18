<?php

namespace App\Http\Controllers\Api\Tracker;

use App\Transformers\Device\DeviceMapTransformer;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Services\ChatService;
use Tobuli\Services\FractalSerializers\ChatDataArraySerializer;
use Tobuli\Services\FractalSerializers\WithoutDataArraySerializer;
use Validator;
use App\Http\Requests\Request;
use Tobuli\Entities\Chat;
use Tobuli\Entities\ChatMessage;

use App\Transformers\ChatMessageTransformer;
use App\Transformers\ChatTransformer;
use CustomFacades\FractalTransformerService;
use FractalTransformer;


class ChatController extends ApiController
{
    /**
     * @var ChatService
     */
    private $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;

        parent::__construct();
    }

    public function initChat()
    {
        $chat = Chat::getRoomByDevice($this->deviceInstance);

        $this->chatService->markAsRead($chat, $this->deviceInstance);

        return response()->json(array_merge(
            ['status' => 1],
            FractalTransformer::item($chat, ChatTransformer::class)->toArray()
        ));
    }

    public function getChattableObjects() {
        return response()->json(['status' => 1, 'data' => $this->deviceInstance->chatableObjects()]);
    }

    public function getMessages() {
        $chat = Chat::getRoomByDevice($this->deviceInstance);

        $this->chatService->markAsRead($chat, $this->deviceInstance);

        return response()->json(array_merge(
            ['status' => 1],
            FractalTransformer::setSerializer(ChatDataArraySerializer::class)->paginate(
                $chat->getLastMessages(),
                ChatMessageTransformer::class
            )->toArray()
        ));
    }

    public function createMessage() {

        $validator = Validator::make(request()->all(), [
            'message' => 'required',
        ]);

        if ( $validator->fails() )
            throw new ValidationException($validator->errors());

        $messageContent = request()->get('message', null);

        $message = new ChatMessage();

        $chat = Chat::getRoomByDevice($this->deviceInstance);

        $message->setTo(null, $chat)->setFrom($this->deviceInstance)->setContent($messageContent)->send();

        return response()->json([
            'status' => 1,
            'message' => FractalTransformer::setSerializer(WithoutDataArraySerializer::class)
                ->item($message, ChatMessageTransformer::class)->toArray()
        ]);
    }
}