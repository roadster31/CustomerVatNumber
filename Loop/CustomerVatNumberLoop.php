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
 * Date: 04/07/2019 23:15
 */
namespace CustomerVatNumber\Loop;

use CustomerVatNumber\Model\CustomerVatNumber;
use CustomerVatNumber\Model\CustomerVatNumberQuery;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class CustomerVatNumberLoop
 * @package CustomerVatNumber\Loop
 * @author Franck Allimant <franck@cqfdev.fr>
 * @method int getCustomerId()
 */
class CustomerVatNumberLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('customer_id', null, true)
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $query = CustomerVatNumberQuery::create();

        if (null !== $id = $this->getCustomerId()) {
            $query->filterById($id);
        }

        return $query;
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var CustomerVatNumber $customerVatNumber */
        foreach ($loopResult->getResultDataCollection() as $customerVatNumber) {

            $loopResultRow = new LoopResultRow($customerVatNumber);

            $loopResultRow
                ->set("CUSTOMER_ID", $customerVatNumber->getId())
                ->set("VAT_NUMBER", $customerVatNumber->getVatNumber());

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
