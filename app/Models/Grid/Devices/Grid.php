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

namespace Models\Grid\Devices;

class Grid extends \Models\Grid\Base\Grid {
    use \Traits\Enrichment\Devices;

    public function __construct(int $apiKey) {
        parent::__construct();

        $this->apiKey = $apiKey;
        $this->idsModel = new Ids($apiKey);
        $this->query = new Query($apiKey);
    }

    public function getDevicesByIpId(int $ipId): array {
        $params = [':ip_id' => $ipId];

        return $this->getGrid($this->idsModel->getDevicesIdsByIpId(), $params);
    }

    public function getDevicesByUserId(int $userId): array {
        $params = [':account_id' => $userId];

        return $this->getGrid($this->idsModel->getDevicesIdsByUserId(), $params);
    }

    public function getDevicesByResourceId(int $resourceId): array {
        $params = [':resource_id' => $resourceId];

        return $this->getGrid($this->idsModel->getDevicesIdsByResourceId(), $params);
    }

    public function getAllDevices(): array {
        return $this->getGrid();
    }

    protected function calculateCustomParams(array &$result): void {
        $this->applyDeviceParams($result);
    }

    protected function convertTimeToUserTimezone(array &$result): void {
        $fields = ['created'];

        $this->translateTimeZones($result, $fields);
    }
}
