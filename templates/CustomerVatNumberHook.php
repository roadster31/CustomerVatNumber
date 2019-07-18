<?php

namespace CustomerBirthDate\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Class CustomerBirthDateHook
 * @package CustomerBirthDate\Hook
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class CustomerBirthDateHook extends BaseHook
{
    public function addFormFieldInput(HookRenderEvent $event)
    {
        $event->add($this->render('customer-vat-input.html'));
    }
}
