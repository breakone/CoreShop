<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Mail\Rule\Action;

use CoreShop\Exception;
use CoreShop\Model\Cart;
use CoreShop\Model;
use Pimcore\Model\AbstractModel;
use Pimcore\Model\Document;

/**
 * Class Mail
 * @package CoreShop\Model\Mail\Rule\Action
 */
class Mail extends AbstractAction
{
    /**
     * @var string
     */
    public $type = 'mail';

    /**
     * @var array
     */
    public $mails;

    /**
     * @param AbstractModel $model
     * @param Model\Mail\Rule $rule
     * @param array $params
     *
     * @throws Exception
     */
    public function apply(AbstractModel $model, Model\Mail\Rule $rule, $params = [])
    {
        $language = null;

        if(array_key_exists('language', $params)) {
            $language = $params['language'];
        }
        else if(\Zend_Registry::isRegistered("Zend_Locale")) {
            $language = (string)\Zend_Registry::get("Zend_Locale");
        }

        if(is_null($language)) {
            throw new Exception("Language is not set");
        }

        if(array_key_exists($language, $this->getMails())) {
            $mailDocument = $this->mails[$language];

            $document = Document::getById($mailDocument);

            if($document instanceof Document\Email) {
                $mail = new \CoreShop\Mail();

                $mail->setDocument($mailDocument);
                $mail->setParams($params);
                $mail->setEnableLayoutOnPlaceholderRendering(false);
                $mail->addTo($params['recipient']);
                $mail->send();
            }
        }
    }

    /**
     * @return array
     */
    public function getMails()
    {
        return $this->mails;
    }

    /**
     * @param array $mails
     */
    public function setMails($mails)
    {
        $this->mails = $mails;
    }
}
