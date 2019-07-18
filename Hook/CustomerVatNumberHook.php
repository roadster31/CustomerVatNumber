<?php
/*************************************************************************************/
/*      Copyright (c) Franck Allimant, CQFDev                                        */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE      */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

/**
 * Created by Franck Allimant, CQFDev <franck@cqfdev.fr>
 * Date: 04/07/2019 23:20
 */
namespace CustomerVatNumber\Hook;

use CustomerVatNumber\CustomerVatNumber;
use CustomerVatNumber\Model\CustomerVatNumberQuery;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Translation\Translator;
use Thelia\Model\OrderQuery;

/**
 * Class CustomerVatNumberHook
 * @package CustomerVatNumber\Hook
 * @author  Franck Allimant <franck@cqfdev.fr>
 */
class CustomerVatNumberHook extends BaseHook
{
    public function addFormFieldInput(HookRenderEvent $event)
    {
        $event->add($this->render('customer-vat-input.html'));
    }

    public function onRegisterAddJs(HookRenderEvent $event)
    {
        $event->add($this->render('assets/js/register-move-vat-number-input.html'));
    }

    public function onFrontUpdateAddJs(HookRenderEvent $event)
    {
        $event->add($this->render('assets/js/update-move-vat-number-input.html'));
    }

    public function onFrontCustomerAccountJs(HookRenderEvent $event)
    {
        $event->add(
            $this->render(
                'assets/js/account-display-customer-vat-number.html',
                [ 'customer_id' => $this->getCustomer()->getId() ]
            )
        );
    }

    public function onBackCreate(HookRenderEvent $event)
    {
        $event->add(
            $this->render(
                'customer-vat-input.html',
                [
                    'custVatFormName' => 'thelia.admin.customer.create'
                ]
            )
        );
    }

    public function onBackUpdate(HookRenderEvent $event)
    {
        $event->add(
            $this->render(
                'customer-vat-input.html',
                [
                    'custVatFormName' => 'thelia.admin.customer.update'
                ]
            )
        );
    }

    public function onBackCreateAddJs(HookRenderEvent $event)
    {
        $event->add($this->render('assets/js/create-customer-move-vat-number-input.html'));
    }

    public function onBackUpdateAddJs(HookRenderEvent $event)
    {
        $event->add($this->render('assets/js/update-customer-move-vat-number-input.html'));
    }


    public function invoiceInformation(HookRenderBlockEvent $event)
    {
        if (null !== $order = OrderQuery::create()->findPk($event->getArgument('order'))) {
            if (null !== $cv = CustomerVatNumberQuery::create()->findOneById($order->getCustomerId())) {
                $vatNumber = $cv->getVatNumber();

                if (! empty($vatNumber)) {
                    $event->add([
                        'title' => Translator::getInstance()->trans("Your VAT Number", [], CustomerVatNumber::DOMAIN_NAME),
                        'value' => $vatNumber
                    ]);
                }
            }
        }
    }
}
