<?php namespace ModalHelpers;

use CustomFacades\Repositories\DeviceRepo;
use CustomFacades\Repositories\EventCustomRepo;
use CustomFacades\Repositories\TrackerPortRepo;
use CustomFacades\Validators\EventCustomFormValidator;
use Illuminate\Support\Facades\DB;
use Tobuli\Exceptions\ValidationException;

class CustomEventModalHelper extends ModalHelper
{
    public function get()
    {
        $this->checkException('custom_events', 'view');

        $this->data['filter']['user_id'] = $this->user->id;
        $events = EventCustomRepo::searchAndPaginate($this->data, 'id', 'desc', 10);

        if ($this->api) {
            $events = $events->toArray();
            $events['url'] = route('api.get_custom_events');
        }

        return compact('events');
    }

    public function createData()
    {
        $this->checkException('custom_events', 'create');

        $protocols = TrackerPortRepo::getProtocolList();

        $types = [
            '1' => trans('front.event_type_1'),
            '2' => trans('front.event_type_2'),
            '3' => trans('front.event_type_3')
        ];

        if ($this->api) {
            $protocols = apiArray($protocols);
            $types = apiArray($types);
        }

        return compact('protocols', 'types');
    }

    public function create()
    {
        $this->checkException('custom_events', 'store');

        EventCustomFormValidator::validate('create', $this->data);

        $insert = FALSE;
        if ($this->api && isset($this->data['conditions'])) {
            $this->data['tags'] = json_decode($this->data['conditions'], true);
            $this->data['conditions'] = [];
            foreach($this->data['tags'] as $key => $val) {
                $tag = strtolower($val['tag']);
                $type = $val['type'];
                $tag_value = $val['tag_value'];
                if ($tag == '' && $tag_value == '')
                    continue;

                if ($tag == '' || $tag_value == '')
                    throw new ValidationException(['conditions' => trans('front.fill_all_fields')]);

                $insert = TRUE;

                if (empty($this->data['conditions']))
                    $this->data['conditions'] = [];

                $this->data['conditions'][] = [
                    'tag' => $tag,
                    'type' => $type,
                    'tag_value' => $tag_value
                ];
            }
        }
        else {
            foreach($this->data['tag'] as $key => $tag) {
                $tag = strtolower($tag);
                $type = $this->data['type'][$key] ?? '';
                $tag_value = $this->data['tag_value'][$key] ?? '';
                if ($tag == '' && $tag_value == '')
                    continue;

                if ($tag == '' || $tag_value == '' || $type === '')
                    throw new ValidationException(['conditions' => trans('front.fill_all_fields')]);

                $insert = TRUE;

                if (empty($this->data['conditions']))
                    $this->data['conditions'] = [];

                $this->data['conditions'][] = [
                    'tag' => $tag,
                    'type' => $type,
                    'tag_value' => $tag_value
                ];
            }
        }

        if (!$insert)
            throw new ValidationException(['conditions' => trans('front.fill_all_fields')]);

        $item = EventCustomRepo::create($this->data + ['user_id' => $this->user->id, 'always' => isset($this->data['alawys'])]);

        $tags_arr = [];
        foreach ($this->data['conditions'] as $condition) {
            $tags_arr[$condition['tag']] = [
                'event_custom_id' => $item->id,
                'tag' => $condition['tag']
            ];
        }
        DB::table('event_custom_tags')->insert($tags_arr);

        return ['status' => 1, 'item' => $item];
    }

    public function editData()
    {
        $id = array_key_exists('custom_event_id', $this->data) ? $this->data['custom_event_id'] : request()->route('custom_events');

        $item = EventCustomRepo::find($id);

        $this->checkException('custom_events', 'edit', $item);

        $protocols = TrackerPortRepo::getProtocolList();

        $types = [
            '1' => trans('front.event_type_1'),
            '2' => trans('front.event_type_2'),
            '3' => trans('front.event_type_3')
        ];

        if ($this->api) {
            $protocols = apiArray($protocols);
            $types = apiArray($types);
        }

        return compact('item', 'protocols', 'types');
    }

    public function edit()
    {
        $item = EventCustomRepo::find($this->data['id']);

        $this->checkException('custom_events', 'update', $item);

        EventCustomFormValidator::validate('update', $this->data);

        $insert = FALSE;
        $tags_arr = [];
        foreach($this->data['tag'] as $key => $tag) {
            $tag = strtolower($tag);
            $type = $this->data['type'][$key] ?? '';
            $tag_value = $this->data['tag_value'][$key] ?? '';
            if ($tag == '' && $tag_value == '')
                continue;

            if ($tag == '' || $tag_value == '' || $type === '')
                throw new ValidationException(['conditions' => trans('front.fill_all_fields')]);

            $insert = TRUE;

            $tags_arr[$tag] = [
                'event_custom_id' => $item->id,
                'tag' => $tag
            ];

            if (empty($this->data['conditions']))
                $this->data['conditions'] = [];

            $this->data['conditions'][] = [
                'tag' => $tag,
                'type' => $type,
                'tag_value' => $tag_value
            ];
        }

        if (!$insert)
            throw new ValidationException(['conditions' => trans('front.fill_all_fields')]);

        EventCustomRepo::update($item->id, $this->data + ['always' => isset($this->data['alawys'])]);
        $item->tags()->delete();
        DB::table('event_custom_tags')->insert($tags_arr);

        return ['status' => 1];
    }

    public function doDestroy($id)
    {
        $item = EventCustomRepo::find($id);

        $this->checkException('custom_events', 'remove', $item);

        return compact('item');
    }

    public function destroy()
    {
        $id = array_key_exists('custom_event_id', $this->data) ? $this->data['custom_event_id'] : $this->data['id'];

        $item = EventCustomRepo::find($id);

        $this->checkException('custom_events', 'remove', $item);

        EventCustomRepo::delete($id);
        
        return ['status' => 1];
    }

    public function getGroupedEvents($devices)
    {
        $devicesProtocols = $devices ? array_unique($devices->groupProtocols()->get()->pluck('protocol')->all()) : [];

        $groups = [];

        $items = EventCustomRepo::getWhereInWhere($devicesProtocols, 'protocol', ['user_id' => null])->pluck('message_with_protocol', 'id')->all();
        $groups[] = [
            'key'   => 'system',
            'name'  => trans('front.system_events'),
            'items' => $items
        ];

        $items = EventCustomRepo::getWhereInWhere($devicesProtocols, 'protocol', ['user_id' => $this->user->id])->pluck('message_with_protocol', 'id')->all();
        $groups[] = [
            'key'   => 'custom',
            'name'  => trans('front.custom_events'),
            'items' => $items
        ];

        return $groups;
    }
}