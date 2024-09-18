<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Tobuli\Entities\Chat;
use Tobuli\Entities\ChatMessage;
use Tobuli\Entities\Device;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Services\ChatService;

class ChatController extends Controller
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

    protected function getChattableObjects()
    {
        return $this->user->devices()
            ->search(Request::input('search_phrase'))
            ->chatable()
            ->withCount([
                'messages' => function($query) {
                    $query->unread()->whereChattable($this->user);
                }
            ])
            ->clearOrdersBy()
            ->orderBy('messages_count', 'desc')
            ->orderBy('devices.name')
            ->paginate(10);
    }

    public function index()
    {
        $this->checkException('chats', 'view');

        $chattableObjects = $this->getChattableObjects();

        return view('front::Chat.index')->with(compact('chattableObjects'));
    }

    public function searchParticipant()
    {
        $this->checkException('chats', 'view');

        $chattableObjects = $this->getChattableObjects();

        return view('front::Chat.partials.table')->with(compact('chattableObjects'));
    }

    public function getChat($chatId)
    {
        $chat = Chat::with(['participants'])->find($chatId);

        $this->checkException('chats', 'show', $chat);

        $this->chatService->markAsRead($chat, $this->user);

        return view('front::Chat.partials.conversation')
            ->with([
                'chat' => $chat,
                'messages' => $chat->getLastMessages(),
            ]);
    }

    public function getMessages($chatId)
    {
        $chat = Chat::find($chatId);

        $this->checkException('chats', 'show', $chat);

        $time = request()->get('time');

        $messages = $chat
            ->messages()
            ->when($time, function($query) use ($time) {
                return $query->since($time);
            })
            ->reversePaginate();

        return response()->json(array_merge([
            'status' => 1,
            'timestamp' => time()
        ], $messages->toArray()));
    }

    public function getUnreadMessagesCount()
    {
        $this->checkException('chats', 'view');

        return response()->json([
            'status' => 1,
            'count' => $this->chatService->getUnreadMessageCount($this->user)
        ]);
    }

    public function initChat($chatableId, $type = 'device')
    {
        $this->checkException('chats', 'store');

        switch ($type) {
            case 'device':
                $device = Device::find($chatableId);

                if ( ! $device)
                    throw new ModelNotFoundException('Device not found');

                $chat = Chat::getRoomByDevice($device);
                $chat->addParticipant($this->user);

                break;
            case 'user':
                $user = User::find($chatableId);
                if ( ! $user)
                    throw new ModelNotFoundException('User not found');

                $participants = new Collection();
                $participants->push($user);
                $participants->push($this->user);

                $chat = Chat::getRoom($participants);

                break;
            default:
                throw new \Exception("Type '$type' not supported");
        }

        $this->chatService->markAsRead($chat, $this->user);

        return view('front::Chat.partials.conversation')->with([
                'chat' => $chat,
                'messages' => $chat->getLastMessages()->setPath(route('chat.messages', $chat->id))
            ]);
    }

    public function createMessage($chatId) {
        if (empty($this->data['message'])) {
            throw new ValidationException(['message' => trans('validation.attributes.message')]);
        }

        $chat = Chat::find($chatId);

        $this->checkException('chats', 'update', $chat);

        $message = new ChatMessage();
        $message
            ->setTo(null, $chat)
            ->setFrom($this->user)
            ->setContent($this->data['message'])
            ->send();

        return response()->json(['status' => 1, 'message' => $message]);
    }
}
