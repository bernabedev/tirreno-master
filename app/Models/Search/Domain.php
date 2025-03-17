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

namespace Models\Search;

class Domain extends \Models\BaseSql {
    protected $DB_TABLE_NAME = 'event_domain';

    public function searchByDomain(string $query, int $apiKey): array {
        $params = [
            ':api_key' => $apiKey,
            ':query' => "%{$query}%",
        ];

        $query = (
            "SELECT
                event_domain.id     AS id,
                'Domain'             AS \"groupName\",
                'domain'             AS \"entityId\",
                event_domain.domain AS value

            FROM
                event_domain

            WHERE
                event_domain.key = :api_key
                AND LOWER(event_domain.domain) LIKE LOWER(:query)

            LIMIT 25 OFFSET 0"
        );

        return $this->execQuery($query, $params);
    }
}
