<?php namespace ModalHelpers;

use CustomFacades\Repositories\BillingPlanRepo;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Validators\RegistrationFormValidator;
use Tobuli\Entities\EmailTemplate;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag as Bugsnag;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Services\UserService;

class RegistrationModalHelper extends ModalHelper
{
    public function create()
    {
        $userService = new UserService();

        try {
            RegistrationFormValidator::validate('create', $this->data);
        } catch (ValidationException $e) {
            return [
                'status' => 0,
                'errors' => $e->getErrors()
            ];
        }


        $password =  $userService->generatePassword();

        $item = $userService->registration([
            'email'    => $this->data['email'],
            'password' => $password
        ]);

        $item['password_to_email'] = $password;
        $this->sendRegistrationEmail($item);

        return ['status' => 1, 'message' => trans('front.registration_successful')];
    }

    public function sendRegistrationEmail($item)
    {
        $email_template = EmailTemplate::getTemplate('registration', $item);

        try {
            sendTemplateEmail($item->email, $email_template, $item);
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);
        }
    }
}