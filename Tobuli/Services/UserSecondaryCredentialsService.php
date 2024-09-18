<?php

namespace Tobuli\Services;

use CustomFacades\Validators\UserSecondaryCredentialsValidator;
use Tobuli\Entities\EmailTemplate;
use Tobuli\Entities\UserSecondaryCredentials;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\Password;

class UserSecondaryCredentialsService
{
    private bool $notifyUser = false;

    public function store(array $data): UserSecondaryCredentials
    {
        $this->normalize($data);

        UserSecondaryCredentialsValidator::validate('create', $data);

        $item = new UserSecondaryCredentials($data);
        $item->save();

        if ($this->notifyUser) {
            $this->notifyUser($data, $item, 'account_created');
        }

        return $item;
    }

    public function update(array $data, UserSecondaryCredentials $item): bool
    {
        $this->normalize($data);

        UserSecondaryCredentialsValidator::validate('update', $data, $item->id);

        $success = $item->update($data);

        if ($this->notifyUser) {
            $this->notifyUser($data, $item, 'account_password_changed');
        }

        return $success;
    }

    public function delete(UserSecondaryCredentials $item): ?bool
    {
        return $item->delete();
    }

    private function normalize(array &$data)
    {
        if (!empty($data['password_generate'])) {
            $data['password'] = $data['password_confirmation'] = Password::generate();
        }
    }

    private function notifyUser(array $data, UserSecondaryCredentials $credentials, string $template)
    {
        $template = EmailTemplate::getTemplate($template, $credentials->user);

        try {
            sendTemplateEmail($data['email'], $template, $data);
        } catch (\Exception $e) {
            throw new ValidationException(['id' => 'Failed to send notify mail. Check email settings.']);
        }
    }

    public function setNotifyUser(bool $notifyUser): self
    {
        $this->notifyUser = $notifyUser;

        return $this;
    }
}