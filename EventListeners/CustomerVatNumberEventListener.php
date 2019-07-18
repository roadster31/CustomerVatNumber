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
 * Date: 04/07/2019 23:14
 */
namespace CustomerVatNumber\EventListeners;

use CustomerVatNumber\CustomerVatNumber;
use CustomerVatNumber\Model\CustomerVatNumberQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Core\Event\Customer\CustomerEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;

/**
 * Class CustomerVatNumberEventListener
 * @package CustomerVatNumber\EventListeners
 * @author  Franck Allimant <franck@cqfdev.fr>
 */
class CustomerVatNumberEventListener implements EventSubscriberInterface
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function getSubscribedEvents()
    {
        return [
            // En front
            TheliaEvents::FORM_AFTER_BUILD . '.thelia_customer_create' => ['vatNumberInput', 128],
            TheliaEvents::AFTER_CREATECUSTOMER => ['processVatNumber', 128],

            TheliaEvents::FORM_AFTER_BUILD . '.thelia_customer_profile_update' => ['vatNumberInput', 128],
            TheliaEvents::CUSTOMER_UPDATEPROFILE => ['processVatNumber', 128],

            // Dans le BO
            TheliaEvents::FORM_AFTER_BUILD . '.thelia_customer_update' => ['vatNumberInput', 128],
            TheliaEvents::CUSTOMER_UPDATEACCOUNT => ['processVatNumber', 128]
        ];
    }

    /**
     * Add form field input in customer update and create forms
     * @param TheliaFormEvent $event
     */
    public function vatNumberInput(TheliaFormEvent $event)
    {
        if ($this->request->fromApi() === false) {
            $data = $event->getForm()->getFormBuilder()->getData();

            $customerVatNumber = null;

            if (!empty($data['id'])) {
                $customerVatNumber = CustomerVatNumberQuery::create()
                    ->findOneById($data['id']);
            }

            $event->getForm()->getFormBuilder()
                ->add(
                    'vat_number',
                    'text',
                    [
                        'label' => Translator::getInstance()->trans("VAT Number", [], CustomerVatNumber::DOMAIN_NAME),
                        'required' => false,
                        'constraints' => [
                            new Callback([
                                "methods" => [
                                    [$this, "checkVatNumber"],
                                ],
                            ]),
                        ],
                        'data' => ($customerVatNumber !== null) ? $customerVatNumber->getVatNumber() : '',
                        'attr' => [
                        ],
                        'label_attr' => [
                            'help' => Translator::getInstance()->trans("Please enter a valid VAT number.", [], CustomerVatNumber::DOMAIN_NAME),
                            'placeholder' => Translator::getInstance()->trans("VAT Number", [], CustomerVatNumber::DOMAIN_NAME),
                        ]
                    ]
                );
        }
    }

    public function checkVatNumber($value, ExecutionContextInterface $context)
    {
        // @see https://www.oreilly.com/library/view/regular-expressions-cookbook/9781449327453/ch04s21.html
        static $vatNumberRegexp = "/^((AT)?U[0-9]{8}|(BE)?0[0-9]{9}|(BG)?[0-9]{9,10}|(CY)?[0-9]{8}L|(CZ)?[0-9]{8,10}|(DE)?[0-9]{9}|(DK)?[0-9]{8}|(EE)?[0-9]{9}|(EL|GR)?[0-9]{9}|(ES)?[0-9A-Z][0-9]{7}[0-9A-Z]|(FI)?[0-9]{8}|(FR)?[0-9A-Z]{2}[0-9]{9}|(GB)?([0-9]{9}([0-9]{3})?|[A-Z]{2}[0-9]{3})|(HU)?[0-9]{8}|(IE)?[0-9]S[0-9]{5}L|(IT)?[0-9]{11}|(LT)?([0-9]{9}|[0-9]{12})|(LU)?[0-9]{8}|(LV)?[0-9]{11}|(MT)?[0-9]{8}|(NL)?[0-9]{9}B[0-9]{2}|(PL)?[0-9]{10}|(PT)?[0-9]{9}|(RO)?[0-9]{2,10}|(SE)?[0-9]{12}|(SI)?[0-9]{8}|(SK)?[0-9]{10})$/";

        $value = preg_replace("/[^A-Z0-9]/", "", strtoupper($value));

        if (! empty($value)) {
            if (! preg_match($vatNumberRegexp, $value)) {
                $context->addViolation(
                    Translator::getInstance()->trans('Please enter a valid VAT number.', [], CustomerVatNumber::DOMAIN_NAME)
                );
            }
        }
    }

    /**
     * Process the customer VAT number.
     *
     * @param CustomerEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function processVatNumber(CustomerEvent $event)
    {
        if ($this->request->fromApi() === false) {
            // Utilise le principe NON DOCUMENTE qui dit que si une form bindée à un event trouve
            // un champ absent de l'event, elle le rend accessible à travers une méthode magique.
            // (cf. ActionEvent::bindForm())
            $vatNumber = $event->vat_number;

            if (null === $vatNumber) {
                $vatNumber = $this->getVatNumberFromRequest('thelia_customer_profile_update');
            }

            if (null === $vatNumber) {
                $vatNumber = $this->getVatNumberFromRequest('thelia_customer_create');
            }

            if (null !== $vatNumber) {
                if (null === $customerVatNumber = CustomerVatNumberQuery::create()->findOneById($event->getCustomer()->getId())) {
                    // Create a new birth date
                    $customerVatNumber = (new \CustomerVatNumber\Model\CustomerVatNumber())
                        ->setId($event->getCustomer()->getId());
                }

                $customerVatNumber
                    ->setVatNumber($vatNumber)
                    ->save();
            }
        }
    }

    protected function getVatNumberFromRequest($formName)
    {
        $data = $this->request->get($formName, []);

        return isset($data['vat_number']) ? $data['vat_number'] : null;
    }
}
