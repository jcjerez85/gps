<?php

namespace Tobuli\Entities;

interface SecondaryCredentialsInterface
{
    public function setLoginSecondaryCredentials(?UserSecondaryCredentials $credentials): SecondaryCredentialsInterface;

    public function getLoginSecondaryCredentials(): ?UserSecondaryCredentials;

    public function isMainLogin(): bool;
}