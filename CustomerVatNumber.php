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
 * Date: 04/07/2019 23:21
 */
namespace CustomerVatNumber;

use CustomerVatNumber\Model\CustomerVatNumberQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Module\BaseModule;

/**
 * Class CustomerVatNumber
 * @package CustomerVatNumber
 * @author  Franck Allimant <franck@cqfdev.fr>
 */
class CustomerVatNumber extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'customervatnumber';

    public function postActivation(ConnectionInterface $con = null)
    {
        try {
            CustomerVatNumberQuery::create()->findOne();
        } catch (\Exception $ex) {
            $database = new Database($con);

            $database->insertSql(null, [__DIR__ . "/Config/thelia.sql"]);
        }
    }
}
