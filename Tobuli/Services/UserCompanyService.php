<?php

namespace Tobuli\Services;

use CustomFacades\Validators\CompanyValidator;
use Tobuli\Entities\Company;
use Tobuli\Entities\User;

class UserCompanyService
{
    private $user;

    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    public function update(array $input)
    {
        CompanyValidator::validate('write', $input);

        $company = ($this->user->company ?: new Company())
            ->fill($input);
        $company->save();

        if (($this->user->company->id ?? null) !== $company->id) {
            $this->user->company()->associate($company);
            $this->user->save();
        }

        return $company;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}