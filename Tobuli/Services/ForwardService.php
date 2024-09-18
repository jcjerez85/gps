<?php

namespace Tobuli\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\Forward;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Forwards\ForwardsManager;
use Tobuli\InputFields\AbstractField;

class ForwardService
{
    private $validationRules = [
        'active' => 'required|boolean',
        'title'  => 'required|string|max:255',
        'type'   => 'required|string|max:255',
    ];

    /**
     * @var ForwardsManager
     */
    protected $forwardManager;

    public function __construct()
    {
        $this->forwardManager = new ForwardsManager();
    }

    /**
     * @param Forward $item
     * @return \Illuminate\Support\Collection
     */
    public function getTypes(Forward $item)
    {
        $types = collect([]);

        foreach ($this->forwardManager->getEnabledList() as $type) {
            if ($type::getType() === $item->type && $item->payload)
                $type->setConfig($item->payload);

            $types->push([
                'type' => $type::getType(),
                'title' => $type::getTitle(),
                'attributes' => $type->getAttributes()
            ]);
        }

        return $types;
    }

    /**
     * @param array $data
     * @param User|int|null $owner
     * @return Forward
     * @throws ValidationException
     */
    public function store(array $data, $owner)
    {
        $this->validate($data);

        return $this->save(new Forward(), $data, $owner);
    }

    /**
     * @param Forward $forward
     * @param array $data
     * @param User|int|null $owner
     * @return Forward
     * @throws ValidationException
     */
    public function update(Forward $forward, array $data, $owner = null)
    {
        $this->validate($data);

        return $this->save($forward, $data, $owner);
    }

    /**
     * @param array $data
     * @throws ValidationException
     */
    protected function validate(array $data)
    {
        $validator = Validator::make($data, $this->validationRules);

        if ($validator->fails())
            throw new ValidationException($validator->errors());

        $this->forwardManager
            ->resolveType($data['type'])
            ->validate($data);
    }

    /**
     * @param Forward $forward
     * @param array $data
     * @param User|null $owner
     * @return Forward
     */
    protected function save(Forward $forward, array $data, $owner)
    {
        $forward->payload = $this->getPayloadData($data);
        $forward->fill($data);

        if ($owner) {
            $user_id = $owner instanceof User ? $owner->id : (int)$owner;
            $forward->user_id = $user_id;
        }

        $forward->save();

        return $forward;
    }

    protected function getPayloadData(array $data)
    {
        $attributes = $this->forwardManager->resolveType($data['type'])->getAttributes();

        $keys = array_map(function(AbstractField $attribute) {
            return $attribute->getName();
        }, $attributes);

        return Arr::only($data, $keys);
    }
}