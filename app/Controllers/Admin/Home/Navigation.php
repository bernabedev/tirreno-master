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

namespace Controllers\Admin\Home;

class Navigation extends \Controllers\Base {
    use \Traits\ApiKeys;
    use \Traits\Navigation;

    public function showIndexPage(): void {
        $this->redirectIfUnlogged('/login');

        $pageController = new Page();
        $this->response = new \Views\Frontend();
        $this->response->data = $pageController->getPageParams();
    }

    public function getDashboardStat(): array {
        $apiKey = $this->getCurrentOperatorApiKeyId();

        return $apiKey ? (new Data())->getStat($apiKey) : [];
    }

    public function getTopTen(): array {
        $apiKey = $this->getCurrentOperatorApiKeyId();

        return $apiKey ? (new Data())->getTopTen($apiKey) : [];
    }

    public function getChart(): array {
        $apiKey = $this->getCurrentOperatorApiKeyId();

        return $apiKey ? (new Data())->getChart($apiKey) : [];
    }
}
