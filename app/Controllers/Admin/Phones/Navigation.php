<?php

/**
 * Tirreno ~ Open source user analytics
 * Copyright (c) Tirreno Technologies Sàrl (https://www.tirreno.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Tirreno Technologies Sàrl (https://www.tirreno.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.tirreno.com Tirreno(tm)
 */

namespace Controllers\Admin\Phones;

class Navigation extends \Controllers\Base {
    use \Traits\ApiKeys;
    use \Traits\Navigation;

    public function getList(): array {
        $apiKey = $this->getCurrentOperatorApiKeyId();

        return $apiKey ? (new Data())->getList($apiKey) : [];
    }

    public function getPhoneDetails(): array {
        $apiKey = $this->getCurrentOperatorApiKeyId();

        return $apiKey ? (new Data())->getPhoneDetails($apiKey) : [];
    }
}
