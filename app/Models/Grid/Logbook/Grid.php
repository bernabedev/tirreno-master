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

namespace Models\Grid\Logbook;

class Grid extends \Models\Grid\Base\Grid {
    public function __construct(int $apiKey) {
        parent::__construct();

        $this->apiKey = $apiKey;
        $this->idsModel = new Ids($apiKey);
        $this->query = new Query($apiKey);
    }

    public function getAllLogbookEvents() {
        return $this->getGrid();
    }

    protected function convertTimeToUserTimezone(array &$result): void {
        $fields = ['raw_time'];

        // same as translateTimeZones() but with catch to avoid error on non-timestamp values
        $result = array_map(function ($row) use ($fields) {
            try {
                $this->translateTimeZone($row, $fields, true);
            } catch (\Throwable $e) {
            }

            return $row;
        }, $result);
    }
}
