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

namespace Models\Grid\Isps;

class Grid extends \Models\Grid\Base\Grid {
    public function __construct(int $apiKey) {
        parent::__construct();

        $this->apiKey = $apiKey;
        $this->idsModel = new Ids($apiKey);
        $this->query = new Query($apiKey);
    }

    public function getIspsByUserId(int $userId): array {
        $params = [':account_id' => $userId];

        return $this->getGrid($this->idsModel->getIspsIdsByUserId(), $params);
    }

    public function getIspsByDomainId(int $domainId): array {
        $params = [':domain_id' => $domainId];

        return $this->getGrid($this->idsModel->getIspsIdsByDomainId(), $params);
    }

    public function getIspsByCountryId(int $countryId): array {
        $params = [':country_id' => $countryId];

        return $this->getGrid($this->idsModel->getIspsIdsByCountryId(), $params);
    }

    public function getIspsByResourceId(int $resourceId): array {
        $params = [':resource_id' => $resourceId];

        return $this->getGrid($this->idsModel->getIspsIdsByResourceId(), $params);
    }

    public function getAllIsps(): array {
        return $this->getGrid();
    }
}
