<?php

namespace Tobuli\Services;

use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;

class UserClientService
{
    private $user;
    private $validationRules = [
        'first_name' => 'max:50',
        'last_name' => 'max:50',
        'birth_date' => 'date',
        'address' => 'max:255',
    ];

    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    public function update(array $input)
    {
        $client = $this->user->client()->firstOrCreate([]);

        $validator = Validator::make($input, $this->validationRules);

        if ($validator->fails()) {
            throw new ValidationException($validator->messages());
        }

        $client->update($input);

        if (($this->user->client->id ?? null) !== $client->id) {
            $this->user->client()->associate($client);
            $this->user->save();
        }

        return $client;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}