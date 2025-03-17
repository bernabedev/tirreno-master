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

class Query extends \Models\Grid\Base\Query {
    protected $defaultOrder = 'event_ip.lastseen DESC';
    protected $dateRangeField = 'event_ip.lastseen';

    public function getData(): array {
        $queryParams = $this->getQueryParams();

        $query = (
            'SELECT
                event_ip.id,
                event_ip.ip,
                event_ip.fraud_detected,
                event_ip.alert_list,
                event_ip.data_center,
                event_ip.vpn,
                event_ip.tor,
                event_ip.relay,
                event_ip.blocklist,
                event_ip.starlink,
                event_ip.shared      AS total_account,
                event_ip.total_visit,
                event_ip.checked,

                event_ip.lastseen    AS lastseen,

                event_isp.name AS netname,
                event_isp.description,
                event_isp.asn,

                countries.serial,
                countries.id    AS country,
                countries.value AS full_country

            FROM
                event_ip

            LEFT JOIN countries
            ON (event_ip.country = countries.serial)

            LEFT JOIN event_isp
            ON (event_ip.isp = event_isp.id)

            WHERE
                event_ip.key = :api_key
                %s'
        );

        $this->applySearch($query, $queryParams);
        $this->applyOrder($query);
        $this->applyLimit($query, $queryParams);

        return [$query, $queryParams];
    }

    public function getTotal(): array {
        $queryParams = $this->getQueryParams();

        $query = (
            'SELECT
                COUNT (DISTINCT event_ip.ip)

            FROM
                event_ip

            LEFT JOIN countries
            ON (event_ip.country = countries.serial)

            LEFT JOIN event_isp
            ON (event_ip.isp = event_isp.id)

            WHERE
                event_ip.key = :api_key
                %s'
        );

        $this->applySearch($query, $queryParams);

        return [$query, $queryParams];
    }

    private function applySearch(string &$query, array &$queryParams): void {
        $this->applyDateRange($query, $queryParams);

        $search = $this->f3->get('REQUEST.search');
        $searchConditions = $this->injectIdQuery('event_ip.id', $queryParams);

        if (is_array($search) && isset($search['value']) && is_string($search['value']) && $search['value'] !== '') {
            $searchConditions .= (
                ' AND
                (
                    TEXT(event_ip.ip)                   LIKE LOWER(:search_value)
                    OR LOWER(event_isp.asn::text)       LIKE LOWER(:search_value)
                    OR LOWER(event_isp.name)            LIKE LOWER(:search_value)
                    OR LOWER(countries.value)           LIKE LOWER(:search_value)
                    OR LOWER(countries.id)              LIKE LOWER(:search_value)
                )'
            );

            $queryParams[':search_value'] = '%' . $search['value'] . '%';
        }

        //Add search and ids into request
        $query = sprintf($query, $searchConditions);
    }
}
