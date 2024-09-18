<?php

namespace Tobuli\Services\RequiredFields;

use App\Policies\Property\DevicePropertiesPolicy;
use Tobuli\Entities\Device;

class DeviceRequiredFieldsService extends AbstractRequiredFieldsService
{
    protected function isSimActivationDateEnabled(): bool
    {
        if (!$this->getAdditionalInstallationFieldsStatus())
            return false;

        return $this->canEdit('sim_activation_date');
    }
    
    protected function isSimExpirationDateEnabled(): bool
    {
        if (!$this->getAdditionalInstallationFieldsStatus())
            return false;

        return $this->canEdit('sim_expiration_date');
    }
    
    protected function isInstallationDateEnabled(): bool
    {
        if (!$this->getAdditionalInstallationFieldsStatus())
            return false;

        return $this->canEdit('installation_date');
    }
    
    private function getAdditionalInstallationFieldsStatus()
    {
        return settings('plugins.additional_installation_fields.status');
    }

    protected function canEdit($field)
    {
        return (new DevicePropertiesPolicy())->edit(auth()->user(), new Device([]), $field);
    }
}