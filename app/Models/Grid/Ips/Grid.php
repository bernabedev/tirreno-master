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

namespace Models\Grid\Ips;

class Grid extends \Models\Grid\Base\Grid {
    use \Traits\Enrichment\Ips;

    public function __construct(int $apiKey) {
        parent::__construct();

        $this->apiKey = $apiKey;
        $this->idsModel = new Ids($apiKey);
        $this->query = new Query($apiKey);
    }

    public function getIpsByUserId(int $userId): array {
        $params = [':account_id' => $userId];

        return $this->getGrid($this->idsModel->getIpsIdsByUserId(), $params);
    }

    public function getIpsByIspId(int $ispId): array {
        $params = [':isp_id' => $ispId];

        return $this->getGrid($this->idsModel->getIpsIdsByIspId(), $params);
    }

    public function getIpsByDomainId($domainId) {
        $params = [':domain_id' => $domainId];

        return $this->getGrid($this->idsModel->getIpsIdsByDomainId(), $params);
    }

    public function getIpsByCountryId(int $countryId): array {
        $params = [':country_id' => $countryId];

        return $this->getGrid($this->idsModel->getIpsIdsByCountryId(), $params);
    }

    public function getIpsByDeviceId(int $deviceId): array {
        $params = [':device_id' => $deviceId];

        return $this->getGrid($this->idsModel->getIpsIdsByDeviceId(), $params);
    }

    public function getIpsByResourceId(int $resourceId): array {
        $params = [':resource_id' => $resourceId];

        return $this->getGrid($this->idsModel->getIpsIdsByResourceId(), $params);
    }

    public function getAllIps() {
        return $this->getGrid();
    }

    protected function calculateCustomParams(array &$result): void {
        $this->calculateIpType($result);
    }
}
